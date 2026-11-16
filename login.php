<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/google_config.php";

if (auth_is_logged_in()) {
    auth_redirect_to_dashboard();
}

$error = '';
if (isset($_SESSION['auth_expired'])) {
    $error = 'Your session expired due to inactivity. Please log in again.';
    unset($_SESSION['auth_expired']);
}
if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['google_login_csrf'])) {
    $_SESSION['google_login_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $token = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['login_csrf'], $token)) {
        $error = 'Your form session expired. Please refresh and try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password === '') {
        $error = 'Please enter your password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, email, fullname, role, password FROM users WHERE email = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $user = $result ? mysqli_fetch_assoc($result) : null;
                if ($user && password_verify($password, $user['password']) &&
                    in_array($user['role'], ['admin', 'student'], true)) {
                    session_regenerate_id(true);
                    $_SESSION['id'] = (int)$user['id'];
                    $_SESSION['user'] = $user['email'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['LAST_ACTIVITY'] = time();
                    unset($_SESSION['login_csrf']);
                    mysqli_stmt_close($stmt);
                    auth_redirect_to_dashboard();
                }
                $error = 'Invalid email or password.';
            } else {
                $error = 'Login could not be completed. Please try again.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Login could not be completed. Please try again.';
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
<link rel="icon" type="image/svg+xml" href="assets/yes-logo.svg">
<link rel="stylesheet" href="style.css?v=8">
<?php if (GOOGLE_CLIENT_ID !== 'PASTE_YOUR_GOOGLE_WEB_CLIENT_ID_HERE' && GOOGLE_CLIENT_ID !== ''): ?>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<?php endif; ?>
</head>
<body class="auth-page">
<header class="site-header">
 <div class="nav-shell">
  <a class="brand" href="index.php"><img src="assets/yes-logo.svg" alt="Youth Education Support logo"><span>Youth Education <strong>Support</strong></span></a>
  <nav class="main-nav"><a href="index.php">Home</a><a class="nav-login" href="register.php">Create account</a></nav>
 </div>
</header>
<main class="auth-layout">
 <section class="auth-visual">
  <span class="section-kicker">WELCOME BACK</span>
  <h1>Your next opportunity starts with a <em>login.</em></h1>
  <p>Sign in to explore scholarships, learning resources and support for your educational journey.</p>
  <img src="assets/illustrations/learning-community.svg" alt="Illustration of students learning together">
 </section>
 <section class="auth-panel">
  <h2>Sign in to your account</h2>
  <p class="auth-subtitle">Enter your account details below, or continue with Google.</p>
  <?php if ($error !== ''): ?><div class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <?php if (isset($_GET['switched'])): ?><div class="success">You have been signed out. You can now sign in with another account.</div><?php endif; ?>
  <div id="google-signin-error" class="error" role="alert" style="display:none;"></div>
  <?php if (GOOGLE_CLIENT_ID !== 'PASTE_YOUR_GOOGLE_WEB_CLIENT_ID_HERE' && GOOGLE_CLIENT_ID !== ''): ?>
   <div id="googleSignInButton" style="display:flex;justify-content:center;margin:18px 0 8px;"></div>
   <input type="hidden" id="googleCsrfToken" value="<?= htmlspecialchars($_SESSION['google_login_csrf'], ENT_QUOTES, 'UTF-8') ?>">
   <div style="display:flex;align-items:center;gap:12px;margin:16px 0;color:#718096;font-size:13px;"><span style="height:1px;background:#d9e2ec;flex:1;"></span>OR SIGN IN WITH EMAIL<span style="height:1px;background:#d9e2ec;flex:1;"></span></div>
  <?php else: ?>
   <p class="auth-subtitle" style="font-size:13px;">Google sign-in will appear here after you add your Web Client ID to <code>google_config.php</code>.</p>
  <?php endif; ?>
  <form method="POST" action="login.php">
   <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['login_csrf'], ENT_QUOTES, 'UTF-8') ?>">
   <label for="email">Email address</label>
   <input id="email" type="email" name="email" placeholder="you@example.com" autocomplete="username" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
   <label for="password">Password</label>
   <input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
   <button type="submit" name="login" value="1" class="btn btn-orange auth-submit">Login</button>
  </form>
  <p class="auth-register">Don't have an account? <a href="register.php">Register here</a></p>
  <a class="auth-home-link" href="index.php">← Back to home</a>
 </section>
</main>
<?php if (GOOGLE_CLIENT_ID !== 'PASTE_YOUR_GOOGLE_WEB_CLIENT_ID_HERE' && GOOGLE_CLIENT_ID !== ''): ?>
<script>
(function () {
    const errorBox = document.getElementById('google-signin-error');
    function showGoogleError(message) {
        errorBox.textContent = message || 'Google sign-in failed. Please try again.';
        errorBox.style.display = 'block';
    }
    window.handleGoogleCredential = async function (response) {
        errorBox.style.display = 'none';
        try {
            const body = new URLSearchParams();
            body.set('credential', response.credential || '');
            body.set('csrf_token', document.getElementById('googleCsrfToken').value);
            const result = await fetch('google_login.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: body.toString(),
                credentials: 'same-origin'
            });
            const data = await result.json();
            if (!result.ok || !data.success) {
                showGoogleError(data.message || 'Google sign-in could not be completed.');
                return;
            }
            window.location.href = data.redirect;
        } catch (error) {
            showGoogleError('A connection error occurred. Please check your internet connection and try again.');
        }
    };
    window.addEventListener('load', function () {
        if (!window.google || !google.accounts || !google.accounts.id) {
            showGoogleError('Google Sign-In could not load. Check your internet connection.');
            return;
        }
        google.accounts.id.initialize({
            client_id: '<?= htmlspecialchars(GOOGLE_CLIENT_ID, ENT_QUOTES, 'UTF-8') ?>',
            callback: window.handleGoogleCredential,
            auto_select: false
        });
        google.accounts.id.renderButton(document.getElementById('googleSignInButton'), {
            theme: 'outline', size: 'large', text: 'continue_with', shape: 'rectangular', width: 280
        });
    });
})();
</script>
<?php endif; ?>
<script src="darkmode.js"></script>
</body>
</html>
