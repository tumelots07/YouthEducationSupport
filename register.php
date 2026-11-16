
<?php
require_once __DIR__ . "/includes/auth.php";
require_once "db.php";

$message = "";

// Generate CSRF tokens for both registration and the embedded login form.
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
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Youth Education Support</title>
<link rel="icon" type="image/svg+xml" href="assets/yes-logo.svg">
<link rel="stylesheet" href="style.css?v=8">
</head>
<body class="auth-page">
<header class="site-header">
 <div class="nav-shell">
  <a class="brand" href="index.php"><img src="assets/yes-logo.svg" alt="Youth Education Support logo"><span>Youth Education <strong>Support</strong></span></a>
  <nav class="main-nav"><a href="index.php">Home</a><a class="nav-login" href="login.php">Login</a></nav>
 </div>
</header>
<main class="auth-layout">
 <section class="auth-visual">
  <span class="section-kicker">JOIN OUR COMMUNITY</span>
  <h1>Build your future with <em>the right support.</em></h1>
  <p>Create your Youth Education Support account to discover scholarships, learning resources, tutoring and opportunities that can help you succeed.</p>
  <img src="assets/illustrations/learning-community.svg" alt="Illustration representing students learning together">
 </section>
 <section class="auth-panel">
  <h2>Create your account</h2>
  <p class="auth-subtitle">Complete the form below to get started.</p>
  <?php if ($message !== ""): ?><div class="error" role="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <form method="POST" action="register.php">
   <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
   <label for="fullname">Full name</label>
   <input id="fullname" type="text" name="fullname" placeholder="Enter your full name" minlength="3" maxlength="100" value="<?= htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" required>
   <label for="email">Email address</label>
   <input id="email" type="email" name="email" placeholder="you@example.com" maxlength="254" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
   <label for="password">Password</label>
   <input id="password" type="password" name="password" placeholder="At least 8 characters, one uppercase letter and one number" minlength="8" autocomplete="new-password" required>
   <label for="confirm_password">Confirm password</label>
   <input id="confirm_password" type="password" name="confirm_password" placeholder="Re-enter your password" minlength="8" autocomplete="new-password" required>
   <label for="role">Account type</label>
   <select id="role" name="role" required>
    <option value="student" <?= (($_POST['role'] ?? 'student') === 'student') ? 'selected' : '' ?>>Student</option>
    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin (invitation required)</option>
   </select>
   <div id="adminTokenSection" class="admin-token-field" <?= (($_POST['role'] ?? '') === 'admin') ? '' : 'hidden' ?>>
    <label for="admin_token">Administrator invitation token</label>
    <input id="admin_token" type="text" name="admin_token" placeholder="Enter your one-time invitation token" value="<?= htmlspecialchars($_POST['admin_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <small>Admin accounts require a valid one-time invitation from an existing administrator.</small>
   </div>
   <button type="submit" name="register" value="1" class="btn btn-orange auth-submit">Create Account</button>
  </form>
  <p class="auth-register">Already have an account? <a href="login.php">Sign in</a></p>
 </section>
</main>
<footer class="auth-footer">© <?= date('Y') ?> Youth Education Support · <a href="contact.php">Help & Contact</a></footer>
<script>
const roleSelect=document.getElementById('role');
const tokenSection=document.getElementById('adminTokenSection');
const tokenInput=document.getElementById('admin_token');
function updateAdminToken(){const admin=roleSelect.value==='admin';tokenSection.hidden=!admin;tokenInput.required=admin;}
roleSelect.addEventListener('change',updateAdminToken);updateAdminToken();
</script>
</body></html>
