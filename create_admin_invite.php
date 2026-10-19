
<?php
session_start();
require_once "db.php";

// Only an existing administrator may generate invitations.
if (
    !isset($_SESSION['id'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    http_response_code(403);
    exit("Access denied. Administrator access is required.");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$generatedToken = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedCsrf = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $submittedCsrf)) {
        $message = "Your session has expired. Refresh the page and try again.";
    } else {
        // Generate a cryptographically secure, unpredictable token.
        $generatedToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $generatedToken);
        $adminId = (int) $_SESSION['id'];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO admin_invites (token_hash, created_by, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $tokenHash,
                $adminId
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "Invitation created successfully. Copy the token now; it will not be shown again.";
            } else {
                $generatedToken = "";
                $message = "Could not create the invitation. Please try again.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $generatedToken = "";
            $message = "Could not create the invitation. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Invitation | Youth Education Support</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Create Admin Invitation</h2>

    <?php if ($message !== ""): ?>
        <div class="error" role="alert">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <p>This creates a one-time administrator invitation that expires in 24 hours.</p>

    <?php if ($generatedToken !== ""): ?>
        <label for="generatedToken">New invitation token</label>
        <input id="generatedToken" type="text" readonly
            value="<?= htmlspecialchars($generatedToken, ENT_QUOTES, 'UTF-8') ?>">
        <button type="button" id="copyToken">Copy token</button>
        <p><strong>Important:</strong> Save this token now. For security, it is stored as a hash and cannot be retrieved later.</p>
    <?php endif; ?>

    <form method="POST" action="create_admin_invite.php">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Generate New Invitation</button>
    </form>

    <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
</div>

<script>
const copyButton = document.getElementById('copyToken');

if (copyButton) {
    copyButton.addEventListener('click', async function () {
        const tokenField = document.getElementById('generatedToken');

        try {
            await navigator.clipboard.writeText(tokenField.value);
            copyButton.textContent = 'Copied!';
        } catch (error) {
            tokenField.select();
            document.execCommand('copy');
            copyButton.textContent = 'Copy attempted — check the field';
        }
    });
}
</script>

<script src="darkmode.js"></script>
</body>
</html>
