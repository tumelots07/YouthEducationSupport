<?php

require_once __DIR__ . "/includes/auth.php";
include("db.php");

auth_require_role('admin');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Invalid scholarship ID.'); }
$error = '';

$stmt = mysqli_prepare($conn, "SELECT * FROM scholarships WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$row=mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $title = trim($_POST['title'] ?? '');
    $provider = trim($_POST['provider'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $application_url = trim($_POST['application_url'] ?? '');
    if ($application_url !== '' && (!filter_var($application_url, FILTER_VALIDATE_URL) ||
        !in_array(strtolower((string)parse_url($application_url, PHP_URL_SCHEME)), ['http','https'], true))) {
        $error = 'Please enter a valid website URL beginning with https://';
    } else {
        $update = mysqli_prepare($conn, "UPDATE scholarships SET title=?, provider=?, deadline=?, description=?, application_url=? WHERE id=?");
        mysqli_stmt_bind_param($update, "sssssi", $title, $provider, $deadline, $description, $application_url, $id);
        if (mysqli_stmt_execute($update)) { header("Location: manage_scholarships.php"); exit(); }
        $error = 'Could not update this scholarship.';
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Scholarship</title>

<link rel="stylesheet" href="style.css?v=8">

</head>

<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>
<?php $pageHeroTitle = 'Edit Scholarship'; $pageHeroText = 'Update the funding opportunity and its application details.'; $pageHeroImage = 'scholarships.svg'; require __DIR__ . "/includes/page_hero.php"; ?>



<div class="container">

<form method="POST">
<?php if ($error !== ''): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<h2>Edit Scholarship</h2>

<input
type="text"
name="title"
value="<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>"
required>

<input
type="text"
name="provider"
value="<?php echo htmlspecialchars($row['provider'], ENT_QUOTES, 'UTF-8'); ?>"
required>

<input
type="date"
name="deadline"
value="<?php echo $row['deadline']; ?>"
required>

<textarea
name="description"
style="width:100%;height:120px;"
required><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>

<label for="application_url">Official application website link</label>
<input type="url" id="application_url" name="application_url" placeholder="https://www.example.org/apply"
value="<?php echo htmlspecialchars($row['application_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

<button
type="submit"
name="update">

Update Scholarship

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>