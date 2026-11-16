<?php
/**
 * Youth Education Support - shared session and access helpers.
 * Uses PHP's standard session name so existing pages share the same login.
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

$authTimeout = 1800;
if (isset($_SESSION['id'], $_SESSION['LAST_ACTIVITY']) &&
    (time() - (int)$_SESSION['LAST_ACTIVITY']) > $authTimeout) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    session_start();
    $_SESSION['auth_expired'] = true;
}

if (isset($_SESSION['id'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
}

function auth_is_logged_in(): bool {
    return isset($_SESSION['id'], $_SESSION['user'], $_SESSION['fullname'], $_SESSION['role'])
        && in_array($_SESSION['role'], ['admin', 'student'], true);
}
function auth_require_login(): void {
    if (!auth_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
function auth_require_role(string $role): void {
    auth_require_login();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('Access denied. You are not authorised to view this page.');
    }
}
function auth_redirect_to_dashboard(): void {
    if (!auth_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php'));
    exit;
}
