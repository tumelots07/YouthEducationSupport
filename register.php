
<?php
session_start();
require_once "db.php";

$message = "";

// Generate a CSRF token to protect the form.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    $adminToken = trim($_POST['admin_token'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $message = "Your session has expired. Refresh the page and try again.";
    } elseif (mb_strlen($fullname) < 3 || mb_strlen($fullname) > 100) {
        $message = "Full name must be between 3 and 100 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $message = "Please enter a valid email address.";
    } elseif (!in_array($role, ['student', 'admin'], true)) {
        $message = "Please select a valid account type.";
    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $message = "Password must be at least 8 characters and contain an uppercase letter and a number.";
    } elseif ($password !== $confirmPassword) {
        $message = "The passwords do not match.";
    } elseif ($role === 'admin' && $adminToken === '') {
        $message = "An administrator invitation token is required.";
    } else {
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        if (!$check) {
            $message = "Registration could not be completed. Please try again.";
        } else {
            mysqli_stmt_bind_param($check, "s", $email);
            mysqli_stmt_execute($check);
            $existing = mysqli_stmt_get_result($check);

            if ($existing && mysqli_num_rows($existing) > 0) {
                $message = "This email address is already registered.";
            } else {
                $inviteValid = false;
                $tokenHash = null;

                if ($role === 'admin') {
                    // The database stores only the SHA-256 hash of the token.
                    $tokenHash = hash('sha256', $adminToken);
                    mysqli_begin_transaction($conn);

                    $invite = mysqli_prepare(
                        $conn,
                        "SELECT id FROM admin_invites
                         WHERE token_hash = ?
                           AND used_at IS NULL
                           AND expires_at > NOW()
                         LIMIT 1
                         FOR UPDATE"
                    );

                    if ($invite) {
                        mysqli_stmt_bind_param($invite, "s", $tokenHash);
                        mysqli_stmt_execute($invite);
                        $inviteResult = mysqli_stmt_get_result($invite);
                        $inviteRow = $inviteResult
                            ? mysqli_fetch_assoc($inviteResult)
                            : null;

                        $inviteValid = (bool) $inviteRow;
                        mysqli_stmt_close($invite);
                    }

                    if (!$inviteValid) {
                        mysqli_rollback($conn);
                        $message = "The administrator invitation is invalid, expired, or already used.";
                    }
                }

                if ($message === "") {
                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $insert = mysqli_prepare(
                        $conn,
                        "INSERT INTO users (fullname, email, password, role)
                         VALUES (?, ?, ?, ?)"
                    );

                    if (!$insert || $hashedPassword === false) {
                        if ($role === 'admin') {
                            mysqli_rollback($conn);
                        }
                        $message = "Registration could not be completed. Please try again.";
                    } else {
                        mysqli_stmt_bind_param(
                            $insert,
                            "ssss",
                            $fullname,
                            $email,
                            $hashedPassword,
                            $role
                        );

                        if (mysqli_stmt_execute($insert)) {
                            $registrationSucceeded = true;

                            if ($role === 'admin') {
                                $markUsed = mysqli_prepare(
                                    $conn,
                                    "UPDATE admin_invites
                                     SET used_at = NOW()
                                     WHERE id = ? AND used_at IS NULL"
                                );

                                if ($markUsed) {
                                    $inviteId = (int) $inviteRow['id'];
                                    mysqli_stmt_bind_param(
                                        $markUsed,
                                        "i",
                                        $inviteId
                                    );
                                    $registrationSucceeded =
                                        mysqli_stmt_execute($markUsed)
                                        && mysqli_stmt_affected_rows($markUsed) === 1;
                                    mysqli_stmt_close($markUsed);
                                } else {
                                    $registrationSucceeded = false;
                                }

                                if ($registrationSucceeded) {
                                    mysqli_commit($conn);
                                } else {
                                    mysqli_rollback($conn);
                                }
                            }

                            if ($registrationSucceeded) {
                                mysqli_stmt_close($insert);
                                mysqli_stmt_close($check);
                                header("Location: login.php?registered=1");
                                exit();
                            } else {
                                $message = "Registration failed. Please try again.";
                            }
                        } else {
                            if ($role === 'admin') {
                                mysqli_rollback($conn);
                            }
                            $message = "Registration failed. The email may already be in use.";
                        }

                        mysqli_stmt_close($insert);
                    }
                }
            }

            mysqli_stmt_close($check);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Youth Education Support</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <form method="POST" action="register.php">
        <h2>Create an Account</h2>

        <?php if ($message !== ""): ?>
            <div class="error" role="alert">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <label for="fullname">Full Name</label>
        <input id="fullname" type="text" name="fullname"
            placeholder="Enter your full name"
            minlength="3" maxlength="100"
            value="<?= htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="name" required>

        <label for="email">Email Address</label>
        <input id="email" type="email" name="email"
            placeholder="Enter your email address"
            maxlength="254"
            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="email" required>

        <label for="role">Account Type</label>
        <select id="role" name="role" required>
            <option value="student"
                <?= (($_POST['role'] ?? 'student') === 'student') ? 'selected' : '' ?>>
                Student
            </option>
            <option value="admin"
                <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                Admin
            </option>
        </select>

        <div id="adminTokenSection" hidden>
            <label for="admin_token">Admin Invitation Token</label>
            <input id="admin_token" type="password" name="admin_token"
                placeholder="Enter your one-time invitation token"
                autocomplete="off">
            <small>Get this token from an existing administrator. It expires after 24 hours and can only be used once.</small>
        </div>

        <label for="password">Password</label>
        <input id="password" type="password" name="password"
            placeholder="Create a password"
            autocomplete="new-password" required>

        <small class="password-help">
            Password requirements: at least 8 characters, one uppercase letter,
            and one number.
        </small>

        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" type="password" name="confirm_password"
            placeholder="Re-enter your password"
            autocomplete="new-password" required>

        <button type="submit" name="register" value="1">Register</button>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

<script>
const roleSelect = document.getElementById('role');
const adminSection = document.getElementById('adminTokenSection');
const adminToken = document.getElementById('admin_token');

function updateAdminField() {
    const isAdmin = roleSelect.value === 'admin';
    adminSection.hidden = !isAdmin;
    adminToken.required = isAdmin;

    if (!isAdmin) {
        adminToken.value = '';
    }
}

roleSelect.addEventListener('change', updateAdminField);
updateAdminField();
</script>

<script src="darkmode.js"></script>
</body>
</html>
