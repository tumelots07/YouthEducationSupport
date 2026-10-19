
<?php
session_start();
require_once "db.php";

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password === '') {
        $error = "Please enter your password.";
    } else {

        // Use a prepared statement to prevent SQL injection.
        $sql = "SELECT id, email, fullname, role, password
                FROM users
                WHERE email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);

            if (mysqli_stmt_execute($stmt)) {

                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) === 1) {

                    $user = mysqli_fetch_assoc($result);

                    if (password_verify($password, $user['password'])) {

                        // Regenerate the session ID after login.
                        session_regenerate_id(true);

                        $_SESSION['id'] = $user['id'];
                        $_SESSION['user'] = $user['email'];
                        $_SESSION['fullname'] = $user['fullname'];
                        $_SESSION['role'] = $user['role'];

                        if ($user['role'] === 'admin') {
                            header("Location: admin_dashboard.php");
                        } else {
                            header("Location: dashboard.php");
                        }

                        mysqli_stmt_close($stmt);
                        exit();

                    } else {
                        $error = "Invalid email or password.";
                    }

                } else {
                    $error = "Invalid email or password.";
                }

            } else {
                $error = "Login could not be completed. Please try again.";
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Login could not be completed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Youth Education Support</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <form method="POST" action="login.php">

        <h2>Login</h2>

        <?php if ($error !== ""): ?>
            <div class="error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <input
            type="email"
            name="email"
            placeholder="Email Address"
            autocomplete="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required>

        <input
            type="password"
            name="password"
            placeholder="Password"
            autocomplete="current-password"
            required>

        <button type="submit" name="login">
            Login
        </button>

        <p>
            No account?
            <a href="register.php">Register</a>
        </p>

    </form>

</div>

<script src="darkmode.js"></script>

</body>
</html>
