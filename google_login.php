<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/google_config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function google_json_response(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    google_json_response(405, ['success' => false, 'message' => 'Method not allowed.']);
}

$csrf = (string)($_POST['csrf_token'] ?? '');
if (empty($_SESSION['google_login_csrf']) || !hash_equals($_SESSION['google_login_csrf'], $csrf)) {
    google_json_response(403, ['success' => false, 'message' => 'Your sign-in session expired. Refresh the page and try again.']);
}

if (GOOGLE_CLIENT_ID === 'PASTE_YOUR_GOOGLE_WEB_CLIENT_ID_HERE' || GOOGLE_CLIENT_ID === '') {
    google_json_response(500, ['success' => false, 'message' => 'Google Sign-In has not been configured yet. Add your Web Client ID to google_config.php.']);
}

$idToken = trim((string)($_POST['credential'] ?? ''));
if ($idToken === '' || strlen($idToken) > 12000) {
    google_json_response(400, ['success' => false, 'message' => 'Google did not return a valid sign-in token.']);
}

// Validate the ID token with Google's HTTPS tokeninfo endpoint. Never trust
// a name or email supplied separately by the browser.
if (!function_exists('curl_init')) {
    google_json_response(500, ['success' => false, 'message' => 'The PHP cURL extension is required. Enable extension=curl in php.ini and restart Apache.']);
}
$ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($idToken));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    error_log('Google token validation failed: ' . $curlError . ' HTTP ' . $httpCode);
    google_json_response(401, ['success' => false, 'message' => 'Google could not verify your sign-in. Please try again.']);
}
$claims = json_decode($response, true);
if (!is_array($claims)) {
    google_json_response(401, ['success' => false, 'message' => 'Google returned an unreadable sign-in response.']);
}

$issuer = (string)($claims['iss'] ?? '');
$audience = (string)($claims['aud'] ?? '');
$expires = (int)($claims['exp'] ?? 0);
$googleSub = trim((string)($claims['sub'] ?? ''));
$email = strtolower(trim((string)($claims['email'] ?? '')));
$emailVerified = filter_var($claims['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
$googleName = trim((string)($claims['name'] ?? ''));

if (!in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true) ||
    !hash_equals(GOOGLE_CLIENT_ID, $audience) || $expires <= time() ||
    $googleSub === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$emailVerified) {
    google_json_response(401, ['success' => false, 'message' => 'Your Google account details could not be verified.']);
}

// First match an already-linked Google identity. Otherwise link a verified
// Google email to the existing account, preserving its current role.
$stmt = mysqli_prepare($conn, 'SELECT id, email, fullname, role, google_sub FROM users WHERE google_sub = ? LIMIT 1');
if (!$stmt) google_json_response(500, ['success' => false, 'message' => 'Database error while signing in.']);
mysqli_stmt_bind_param($stmt, 's', $googleSub);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$user) {
    $stmt = mysqli_prepare($conn, 'SELECT id, email, fullname, role, google_sub FROM users WHERE LOWER(email) = ? LIMIT 1');
    if (!$stmt) google_json_response(500, ['success' => false, 'message' => 'Database error while signing in.']);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if ($user) {
        // If another Google identity is already attached, do not overwrite it.
        if (!empty($user['google_sub']) && !hash_equals((string)$user['google_sub'], $googleSub)) {
            google_json_response(409, ['success' => false, 'message' => 'This website account is already linked to a different Google account. Sign in using your existing method or contact the administrator.']);
        }
        if (empty($user['google_sub'])) {
            $stmt = mysqli_prepare($conn, 'UPDATE users SET google_sub = ? WHERE id = ? AND google_sub IS NULL');
            if (!$stmt) google_json_response(500, ['success' => false, 'message' => 'Could not link your Google account.']);
            $userId = (int)$user['id'];
            mysqli_stmt_bind_param($stmt, 'si', $googleSub, $userId);
            $updated = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if (!$updated) google_json_response(500, ['success' => false, 'message' => 'Could not link your Google account.']);
        }
    } else {
        // New Google registrations are students only. Google sign-up cannot
        // create or promote an administrator account.
        $fullname = $googleName !== '' ? substr($googleName, 0, 100) : $email;
        $randomPassword = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
        $role = 'student';
        $stmt = mysqli_prepare($conn, 'INSERT INTO users (fullname, email, password, role, google_sub) VALUES (?, ?, ?, ?, ?)');
        if (!$stmt) google_json_response(500, ['success' => false, 'message' => 'Could not create your website account.']);
        mysqli_stmt_bind_param($stmt, 'sssss', $fullname, $email, $randomPassword, $role, $googleSub);
        $created = mysqli_stmt_execute($stmt);
        $newId = (int)mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        if (!$created) {
            // A concurrent first sign-in may have created the same account.
            google_json_response(409, ['success' => false, 'message' => 'Your account could not be created because it may already exist. Please try signing in again.']);
        }
        $user = ['id' => $newId, 'email' => $email, 'fullname' => $fullname, 'role' => 'student'];
    }
}

if (!in_array((string)$user['role'], ['admin', 'student'], true)) {
    google_json_response(403, ['success' => false, 'message' => 'This account does not have permission to sign in.']);
}

session_regenerate_id(true);
$_SESSION['id'] = (int)$user['id'];
$_SESSION['user'] = (string)$user['email'];
$_SESSION['fullname'] = (string)$user['fullname'];
$_SESSION['role'] = (string)$user['role'];
$_SESSION['LAST_ACTIVITY'] = time();
unset($_SESSION['google_login_csrf'], $_SESSION['login_csrf']);

$destination = $user['role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php';
google_json_response(200, ['success' => true, 'redirect' => $destination]);
