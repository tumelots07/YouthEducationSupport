<?php
require_once __DIR__ . "/includes/auth.php";
$switch = isset($_GET['switch']) && $_GET['switch'] === '1';
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
header('Location: ' . ($switch ? 'login.php?switched=1' : 'index.php'));
exit;
