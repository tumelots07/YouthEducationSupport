
<?php
session_start();
require_once "db.php";

// Only logged-in administrators may view contact messages.
if (
    !isset($_SESSION['id'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit();
}

// Retrieve messages, newest first.
$result = mysqli_query(
    $conn,
    "SELECT id, name, email, message, created_at
     FROM contacts
     ORDER BY created_at DESC"
);

$queryError = ($result === false);

if ($queryError) {
    error_log("Contact messages query failed: " . mysqli_error($conn));
}

// Escape values before displaying database content in HTML.
function escapeContactValue($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Contact Messages - Youth Education Support</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .contacts-container {
            width: 95%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .contacts-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
        }

        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .contacts-table th,
        .contacts-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .contacts-table th {
            background: #0077cc;
            color: white;
        }

        .contacts-table td.message-cell {
            min-width: 220px;
            white-space: normal;
        }

        .contacts-notice {
            padding: 12px;
            margin-top: 20px;
            border-radius: 6px;
        }

        .contacts-error {
            background: #f8d7da;
            color: #721c24;
        }

        .contacts-empty {
            background: #eaf4fc;
            color: #234;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<header>
    <h1>Manage Contact Messages</h1>

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

<main class="contacts-container">

    <h2>Submitted Messages</h2>

    <a class="back-link" href="admin_dashboard.php">
        &larr; Back to Admin Dashboard
    </a>

    <?php if ($queryError): ?>

        <p class="contacts-notice contacts-error" role="alert">
            Contact messages could not be loaded.
            Please try again later.
        </p>

    <?php elseif (mysqli_num_rows($result) === 0): ?>

        <p class="contacts-notice contacts-empty">
            There are no contact messages yet.
        </p>

    <?php else: ?>

        <div class="contacts-table-wrapper">
            <table class="contacts-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?= escapeContactValue($row['id']) ?>
                            </td>

                            <td>
                                <?= escapeContactValue($row['name']) ?>
                            </td>

                            <td>
                                <a href="mailto:<?= escapeContactValue($row['email']) ?>">
                                    <?= escapeContactValue($row['email']) ?>
                                </a>
                            </td>

                            <td class="message-cell">
                                <?= nl2br(escapeContactValue($row['message'])) ?>
                            </td>

                            <td>
                                <?= escapeContactValue($row['created_at']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</main>

<script src="darkmode.js"></script>

</body>
</html>

<?php
if (!$queryError && $result !== false) {
    mysqli_free_result($result);
}
?>
