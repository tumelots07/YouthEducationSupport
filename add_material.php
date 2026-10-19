
<?php
session_start();
require_once "db.php";

if (
    !isset($_SESSION['user'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit();
}

$message = "";
$messageType = "";

if (empty($_SESSION['material_csrf_token'])) {
    $_SESSION['material_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = is_string($_POST['title'] ?? null)
        ? trim($_POST['title']) : '';

    $subject = is_string($_POST['subject'] ?? null)
        ? trim($_POST['subject']) : '';

    $description = is_string($_POST['description'] ?? null)
        ? trim($_POST['description']) : '';

    $link = is_string($_POST['link'] ?? null)
        ? trim($_POST['link']) : '';

    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['material_csrf_token'] ?? '';

    $uploadedPath = null;

    if (
        !is_string($token) ||
        !is_string($sessionToken) ||
        $sessionToken === '' ||
        !hash_equals($sessionToken, $token)
    ) {
        $message = "Your form session expired. Refresh the page and try again.";
        $messageType = "error";

    } elseif (
        $title === '' || strlen($title) > 150 ||
        $subject === '' || strlen($subject) > 100 ||
        $description === '' || strlen($description) > 5000
    ) {
        $message = "Please complete all fields within their allowed lengths.";
        $messageType = "error";

    } elseif (
        $link !== '' &&
        (!filter_var($link, FILTER_VALIDATE_URL) ||
         !in_array(strtolower((string) parse_url($link, PHP_URL_SCHEME)), ['http', 'https'], true))
    ) {
        $message = "Enter a valid website link beginning with http:// or https://.";
        $messageType = "error";

    } else {
        $file = $_FILES['pdf_file'] ?? null;
        $hasUpload = is_array($file) &&
            ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($hasUpload && $link !== '') {
            $message = "Please upload a PDF or enter a website link, not both.";
            $messageType = "error";

        } elseif (
            $hasUpload &&
            ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        ) {
            $message = "The PDF upload failed. Please try again.";
            $messageType = "error";

        } elseif (
            $hasUpload &&
            (
                !isset($file['tmp_name'], $file['size']) ||
                !is_uploaded_file($file['tmp_name']) ||
                $file['size'] < 1 ||
                $file['size'] > 5 * 1024 * 1024
            )
        ) {
            $message = "The PDF must be larger than 0 bytes and no bigger than 5 MB.";
            $messageType = "error";

        } elseif ($hasUpload) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $handle = fopen($file['tmp_name'], 'rb');
            $signature = $handle ? fread($handle, 5) : false;

            if ($handle) {
                fclose($handle);
            }

            if ($mimeType !== 'application/pdf' || $signature !== '%PDF-') {
                $message = "Only genuine PDF files are allowed.";
                $messageType = "error";
            } else {
                $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR .
                    'uploads' . DIRECTORY_SEPARATOR . 'materials';

                if (!is_dir($uploadDirectory) &&
                    !mkdir($uploadDirectory, 0755, true) &&
                    !is_dir($uploadDirectory)
                ) {
                    $message = "The upload folder could not be created.";
                    $messageType = "error";
                } else {
                    // Use a random filename rather than the supplied filename.
                    $filename = bin2hex(random_bytes(16)) . '.pdf';
                    $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $uploadedPath = 'uploads/materials/' . $filename;
                        $link = $uploadedPath;
                    } else {
                        $message = "The PDF could not be saved. Check folder permissions.";
                        $messageType = "error";
                    }
                }
            }
        }

        if ($message === '') {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO materials (title, subject, description, link)
                 VALUES (?, ?, ?, ?)"
            );

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssss",
                    $title,
                    $subject,
                    $description,
                    $link
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);

                    $_SESSION['material_csrf_token'] =
                        bin2hex(random_bytes(32));

                    header("Location: manage_materials.php?added=1");
                    exit();
                }

                error_log("Material insert failed: " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
            } else {
                error_log("Material statement failed: " . mysqli_error($conn));
            }

            // Remove the uploaded file if its database record was not saved.
            if ($uploadedPath !== null) {
                $savedFile = __DIR__ . DIRECTORY_SEPARATOR .
                    str_replace('/', DIRECTORY_SEPARATOR, $uploadedPath);

                if (is_file($savedFile)) {
                    unlink($savedFile);
                }
            }

            $message = "The material could not be saved. Please try again.";
            $messageType = "error";
        }
    }
}

function escapeMaterialValue($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Study Material - Youth Education Support</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .form-message.error {
            background: #f8d7da;
            color: #721c24;
        }
        .material-form input,
        .material-form textarea {
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 14px;
        }
        .field-help {
            display: block;
            margin: -8px 0 14px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<header>
    <h1>Add Study Material</h1>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_materials.php">Manage Materials</a>
        <a href="materials.php">View Materials</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <form class="material-form" method="POST"
          action="add_material.php" enctype="multipart/form-data">
        <h2>Add Study Material</h2>

        <?php if ($message !== ''): ?>
            <div class="form-message <?= escapeMaterialValue($messageType) ?>"
                 role="alert">
                <?= escapeMaterialValue($message) ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token"
               value="<?= escapeMaterialValue($_SESSION['material_csrf_token']) ?>">

        <label for="title">Title</label>
        <input id="title" type="text" name="title" maxlength="150"
               value="<?= escapeMaterialValue($_POST['title'] ?? '') ?>" required>

        <label for="subject">Subject</label>
        <input id="subject" type="text" name="subject" maxlength="100"
               value="<?= escapeMaterialValue($_POST['subject'] ?? '') ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" maxlength="5000"
                  required><?= escapeMaterialValue($_POST['description'] ?? '') ?></textarea>

        <label for="pdf_file">Upload PDF (optional)</label>
        <input id="pdf_file" type="file" name="pdf_file"
               accept=".pdf,application/pdf">
        <small class="field-help">PDF files only, maximum size 5 MB.</small>

        <label for="link">External website link (optional)</label>
        <input id="link" type="url" name="link"
               placeholder="https://example.com/resource"
               value="<?= escapeMaterialValue($_POST['link'] ?? '') ?>">
        <small class="field-help">
            Upload a PDF or enter an external link. Do not fill in both.
        </small>

        <button type="submit" name="add">Add Material</button>
    </form>
</div>

<script src="darkmode.js"></script>
</body>
</html>
