
<?php
session_start();
require_once "db.php";

// Only logged-in users may download study materials.
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Accept only a positive integer material ID.
$id = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false || $id === null) {
    http_response_code(404);
    exit("Material not found.");
}

// Fetch the stored record using a prepared statement.
$stmt = mysqli_prepare(
    $conn,
    "SELECT title, link FROM materials WHERE id = ? LIMIT 1"
);

if (!$stmt) {
    error_log("Download query preparation failed: " . mysqli_error($conn));
    http_response_code(500);
    exit("The download could not be completed.");
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (!mysqli_stmt_execute($stmt)) {
    error_log("Download query failed: " . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit("The download could not be completed.");
}

mysqli_stmt_bind_result($stmt, $title, $link);
$found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$found || !is_string($link) || !is_string($title)) {
    http_response_code(404);
    exit("Material not found.");
}

// Do not accept external links, arbitrary filenames or traversal paths.
if (
    preg_match(
        '~\Auploads/materials/[a-f0-9]{32}\.pdf\z~i',
        $link
    ) !== 1
) {
    http_response_code(404);
    exit("PDF not found.");
}

$uploadDirectory = realpath(
    __DIR__ . DIRECTORY_SEPARATOR . 'uploads' .
    DIRECTORY_SEPARATOR . 'materials'
);

$filePath = realpath(
    __DIR__ . DIRECTORY_SEPARATOR .
    str_replace('/', DIRECTORY_SEPARATOR, $link)
);

// Confirm the directory and file exist and the file is inside the
// intended upload directory.
if (
    $uploadDirectory === false ||
    $filePath === false ||
    !is_file($filePath) ||
    !is_readable($filePath) ||
    strpos(
        $filePath,
        $uploadDirectory . DIRECTORY_SEPARATOR
    ) !== 0
) {
    http_response_code(404);
    exit("PDF not found.");
}

// Check the file type again before sending it.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($filePath);

$fileHandle = fopen($filePath, 'rb');
$signature = $fileHandle ? fread($fileHandle, 5) : false;

if ($fileHandle) {
    fclose($fileHandle);
}

if ($mimeType !== 'application/pdf' || $signature !== '%PDF-') {
    error_log("Invalid PDF detected for material ID " . $id);
    http_response_code(404);
    exit("PDF not found.");
}

// Build a safe download filename from the database title.
$safeTitle = preg_replace('/[^A-Za-z0-9_-]+/', '_', $title);
$safeTitle = trim((string)$safeTitle, '_-');

if ($safeTitle === '') {
    $safeTitle = 'study_material';
}

$downloadName = substr($safeTitle, 0, 100) . '.pdf';

if (headers_sent()) {
    http_response_code(500);
    exit("The download could not be completed.");
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

readfile($filePath);
exit();
