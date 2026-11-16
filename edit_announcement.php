<?php

require_once __DIR__ . "/includes/auth.php";
include("db.php");

$id=$_GET['id'];

$result=mysqli_query(
$conn,
"SELECT * FROM announcements WHERE id='$id'");

$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

$title=$_POST['title'];
$message=$_POST['message'];

mysqli_query(
$conn,
"UPDATE announcements
SET title='$title',
message='$message'
WHERE id='$id'");

header("Location: manage_announcements.php");

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Announcement</title>
<link rel="stylesheet" href="style.css?v=8">
</head>
<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>
<?php $pageHeroTitle = 'Edit Announcement'; $pageHeroText = 'Update an announcement before sharing it with the student community.'; $pageHeroImage = 'announcements.svg'; require __DIR__ . "/includes/page_hero.php"; ?>



<div class="container">

<form method="POST">

<h2>Edit Announcement</h2>

<input
type="text"
name="title"
value="<?php echo $row['title']; ?>"
required>

<textarea
name="message"
style="width:100%;height:120px;"
required><?php echo $row['message']; ?></textarea>

<button
type="submit"
name="update">

Update Announcement

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>