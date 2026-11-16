<?php
require_once __DIR__ . "/includes/auth.php";
auth_require_login();
require_once __DIR__ . "/db.php";

$message = '';
$error = '';
$email = $_SESSION['user'];

$stmt = mysqli_prepare($conn, "SELECT id, fullname, email, role, password FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$row) { session_destroy(); header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($fullname === '' || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid full name and email address.';
    } elseif ($current_password !== '' || $new_password !== '' || $confirm_password !== '') {
        if ($current_password === '') $error = 'Enter your current password to change your password.';
        elseif (!password_verify($current_password, $row['password'])) $error = 'Your current password is incorrect.';
        elseif (strlen($new_password) < 8) $error = 'Your new password must contain at least 8 characters.';
        elseif ($new_password !== $confirm_password) $error = 'The new password and confirmation do not match.';
    }

    if ($error === '') {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id <> ?");
        mysqli_stmt_bind_param($check, "si", $new_email, $row['id']);
        mysqli_stmt_execute($check);
        $exists = mysqli_stmt_get_result($check);
        if (mysqli_num_rows($exists) > 0) $error = 'That email address is already used by another account.';
        mysqli_stmt_close($check);
    }

    if ($error === '') {
        if ($new_password !== '') {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = mysqli_prepare($conn, "UPDATE users SET fullname = ?, email = ?, password = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "sssi", $fullname, $new_email, $hash, $row['id']);
        } else {
            $update = mysqli_prepare($conn, "UPDATE users SET fullname = ?, email = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "ssi", $fullname, $new_email, $row['id']);
        }
        if (mysqli_stmt_execute($update)) {
            $_SESSION['fullname'] = $fullname;
            $_SESSION['user'] = $new_email;
            $email = $new_email;
            $message = 'Profile updated successfully.';
            $refresh = mysqli_prepare($conn, "SELECT id, fullname, email, role, password FROM users WHERE id = ?");
            mysqli_stmt_bind_param($refresh, "i", $row['id']);
            mysqli_stmt_execute($refresh);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($refresh));
            mysqli_stmt_close($refresh);
        } else $error = 'Unable to update your profile. Please try again.';
        mysqli_stmt_close($update);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | Youth Education Support</title>
<link rel="stylesheet" href="style.css?v=8">
</head>
<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>
<header><h1>My Profile</h1></header>
<?php $pageHeroTitle = 'My Profile'; $pageHeroText = 'Manage your account details and keep your password secure.'; $pageHeroImage = 'https://images.pexels.com/photos/6283211/pexels-photo-6283211.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>
<main class="container profile-container">
<form method="POST" autocomplete="off">
<h2>Profile Information</h2>
<?php if ($message !== ''): ?><div class="success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<label for="fullname">Full Name</label>
<input id="fullname" type="text" name="fullname" value="<?= htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8') ?>" required>
<label for="email">Email Address</label>
<input id="email" type="email" name="email" value="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>" required>
<label for="role">Role</label>
<input id="role" type="text" value="<?= htmlspecialchars(ucfirst($row['role']), ENT_QUOTES, 'UTF-8') ?>" readonly>
<hr>
<h2>Change Password</h2>
<p>Leave the password fields blank if you are not changing your password. Enter your current password and confirm your new password to make a change.</p>
<label for="current_password">Current Password</label>
<input id="current_password" type="password" name="current_password" autocomplete="current-password" placeholder="Enter your current password">
<label for="new_password">New Password</label>
<input id="new_password" type="password" name="new_password" minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
<label for="confirm_password">Confirm New Password</label>
<input id="confirm_password" type="password" name="confirm_password" minlength="8" autocomplete="new-password" placeholder="Re-enter your new password">
<button type="submit" name="update">Save Profile Changes</button>
</form>
</main>
<script src="darkmode.js"></script>
</body>
</html>
