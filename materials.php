
<?php
session_start();
require_once "db.php";

// Study materials are available only to logged-in users.
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query(
    $conn,
    "SELECT id, title, subject, description, link
     FROM materials
     ORDER BY id DESC"
);

$queryError = ($result === false);

if ($queryError) {
    error_log("Materials query failed: " . mysqli_error($conn));
}

function escapeMaterial($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Only links using HTTP or HTTPS are shown as external resources.
function isSafeExternalLink(string $link): bool
{
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = strtolower((string)parse_url($link, PHP_URL_SCHEME));

    return in_array($scheme, ['http', 'https'], true);
}

// Uploaded PDFs use a random 32-character hexadecimal filename.
function isUploadedPdfPath(string $link): bool
{
    return preg_match(
        '~\Auploads/materials/[a-f0-9]{32}\.pdf\z~i',
        $link
    ) === 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Study Materials - Youth Education Support</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .material-card {
            margin-bottom: 20px;
            overflow-wrap: anywhere;
        }

        .material-action {
            display: inline-block;
            margin: 8px 8px 0 0;
            text-decoration: none;
        }

        .materials-notice {
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .materials-error {
            background: #f8d7da;
            color: #721c24;
        }

        .materials-empty {
            background: #eaf4fc;
            color: #234;
        }
    </style>
</head>

<body>
<header>
    <h1>Study Materials</h1>

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

<main class="container">
    <h2>Available Learning Resources</h2>
    <p>Browse your study resources and download available PDF notes.</p>

    <?php if ($queryError): ?>

        <p class="materials-notice materials-error" role="alert">
            Study materials could not be loaded. Please try again later.
        </p>

    <?php elseif (mysqli_num_rows($result) === 0): ?>

        <p class="materials-notice materials-empty">
            No study materials are available yet.
        </p>

    <?php else: ?>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
                $link = trim((string)($row['link'] ?? ''));
                $isPdf = isUploadedPdfPath($link);
                $isExternal = $link !== '' && isSafeExternalLink($link);
            ?>

            <section class="card material-card">
                <h2><?= escapeMaterial($row['title']) ?></h2>

                <p>
                    <strong>Subject:</strong>
                    <?= escapeMaterial($row['subject']) ?>
                </p>

                <p>
                    <?= nl2br(escapeMaterial($row['description'])) ?>
                </p>

                <?php if ($isPdf): ?>

                    <a class="material-action"
                       href="download_material.php?id=<?= (int)$row['id'] ?>">
                        <button type="button">Download PDF</button>
                    </a>

                <?php elseif ($isExternal): ?>

                    <a class="material-action"
                       href="<?= escapeMaterial($link) ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        <button type="button">Open External Resource</button>
                    </a>

                <?php elseif ($link === ''): ?>

                    <p>This resource does not have a download or external link yet.</p>

                <?php else: ?>

                    <p>This resource link is unavailable. Please contact the administrator.</p>

                <?php endif; ?>
            </section>
        <?php endwhile; ?>

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
