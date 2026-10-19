
<?php
session_start();
require_once "db.php";

$message = "";

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate the full name.
    if (mb_strlen($fullname) < 3) {
        $message = "Full name must be at least 3 characters.";
    } elseif (mb_strlen($fullname) > 100) {
        $message = "Full name cannot exceed 100 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($email) > 254) {
        $message = "Email address is too long.";
    } elseif (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        $message = "Password must contain at least 8 characters, one uppercase letter and one number.";
    } else {
        // Check whether the email already exists.
        $checkSql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $checkStmt = mysqli_prepare($conn, $checkSql);

        if (!$checkStmt) {
            $message = "Registration could not be completed. Please try again.";
        } else {
            mysqli_stmt_bind_param($checkStmt, "s", $email);

            if (!mysqli_stmt_execute($checkStmt)) {
                $message = "Registration could not be completed. Please try again.";
            } else {
                $result = mysqli_stmt_get_result($checkStmt);

                if ($result && mysqli_num_rows($result) > 0) {
                    $message = "Email already exists.";
                } else {
                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    if ($hashedPassword === false) {
                        $message = "Registration could not be completed. Please try again.";
                    } else {
                        // Public registration is for students only.
                        $role = "student";

                        $insertSql = "INSERT INTO users
                            (fullname, email, password, role)
                            VALUES (?, ?, ?, ?)";

                        $insertStmt = mysqli_prepare($conn, $insertSql);

                        if ($insertStmt) {
                            mysqli_stmt_bind_param(
                                $insertStmt,
                                "ssss",
                                $fullname,
                                $email,
                                $hashedPassword,
                                $role
                            );

                            if (mysqli_stmt_execute($insertStmt)) {
                                mysqli_stmt_close($insertStmt);
                                mysqli_stmt_close($checkStmt);

                                header("Location: login.php");
                                exit();
                            } else {
                                $message = "Registration failed. Please try again.";
                            }

                            mysqli_stmt_close($insertStmt);
                        } else {
                            $message = "Registration could not be completed. Please try again.";
                        }
                    }
                }
            }

            mysqli_stmt_close($checkStmt);
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
        <h2>Register</h2>

        <?php if ($message !== ""): ?>
            <div class="error" role="alert">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <input
            type="text"
            name="fullname"
            placeholder="Full Name"
            minlength="3"
            maxlength="100"
            value="<?= htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="name"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Email Address"
            maxlength="254"
            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password (at least 8 characters, 1 uppercase and 1 number)"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <button type="submit" name="register" value="1">
            Register
        </button>

        <p>
            Already have an account?
            <a href="login.php">Login</a>
        </p>
    </form>
</div>

<script src="darkmode.js"></script>
</body>
</html>
