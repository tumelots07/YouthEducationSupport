
<?php
session_start();
require_once "db.php";

$message = "";
$messageType = "";

// Generate a CSRF token for form protection.
if (empty($_SESSION['contact_csrf_token'])) {
    $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));
}

// Display the success message after redirecting.
if (isset($_GET['sent']) && $_GET['sent'] === '1') {
    $message = "Message sent successfully.";
    $messageType = "success";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Make sure submitted fields are strings.
    $name = is_string($_POST['name'] ?? null)
        ? trim($_POST['name']) : '';

    $email = is_string($_POST['email'] ?? null)
        ? trim($_POST['email']) : '';

    $msg = is_string($_POST['message'] ?? null)
        ? trim($_POST['message']) : '';

    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['contact_csrf_token'] ?? '';

    // Validate the CSRF token.
    if (
        !is_string($submittedToken) ||
        !is_string($sessionToken) ||
        $sessionToken === '' ||
        !hash_equals($sessionToken, $submittedToken)
    ) {
        $message = "Your form session expired. Please refresh the page and try again.";
        $messageType = "error";

    } elseif (
        $name === '' ||
        strlen($name) > 100
    ) {
        $message = "Please enter a name of up to 100 characters.";
        $messageType = "error";

    } elseif (
        $email === '' ||
        strlen($email) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif (
        $msg === '' ||
        !preg_match('/\S/u', $msg) ||
        strlen($msg) > 5000
    ) {
        $message = "Please enter a message of up to 5,000 characters.";
        $messageType = "error";

    } else {
        // Prepared statements protect the SQL query.
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO contacts (name, email, message)
             VALUES (?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $msg);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                // Generate a new token after successful submission.
                $_SESSION['contact_csrf_token'] =
                    bin2hex(random_bytes(32));

                // Redirect to prevent duplicate submissions on refresh.
                header("Location: contact.php?sent=1");
                exit();

            } else {
                error_log(
                    "Contact insert failed: " . mysqli_stmt_error($stmt)
                );

                mysqli_stmt_close($stmt);

                $message = "Your message could not be sent. Please try again later.";
                $messageType = "error";
            }

        } else {
            error_log(
                "Contact statement preparation failed: " . mysqli_error($conn)
            );

            $message = "Your message could not be sent. Please try again later.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us - Youth Education Support</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .contact-message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .contact-message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .contact-message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

<header>

    <h1>Contact Us</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="scholarships.php">Scholarships</a>
        <a href="materials.php">Materials</a>
        <a href="announcements.php">Announcements</a>
        <a href="tutors.php">Tutors</a>
        <a href="volunteer.php">Volunteer</a>
        <a href="donations.php">Donations</a>
        <a href="events.php">Events</a>
        <a href="contact.php">Contact</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>

</header>

<div class="container">

    <form method="POST" action="contact.php">

        <h2>Send Message</h2>

        <?php if ($message !== ''): ?>
            <div
                class="contact-message <?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>"
                role="<?= $messageType === 'error' ? 'alert' : 'status' ?>"
            >
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['contact_csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <input
            type="text"
            name="name"
            placeholder="Name"
            maxlength="100"
            value="<?= htmlspecialchars(
                is_string($_POST['name'] ?? null)
                    ? $_POST['name'] : '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Email"
            maxlength="100"
            value="<?= htmlspecialchars(
                is_string($_POST['email'] ?? null)
                    ? $_POST['email'] : '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            required
        >

        <textarea
            name="message"
            placeholder="Write your message here..."
            maxlength="5000"
            style="width:100%;height:120px;"
            required><?= htmlspecialchars(
                is_string($_POST['message'] ?? null)
                    ? $_POST['message'] : '',
                ENT_QUOTES,
                'UTF-8'
            ) ?></textarea>

        <button type="submit" name="send">
            Send Message
        </button>

    </form>

</div>

<script src="darkmode.js"></script>

</body>
</html>
