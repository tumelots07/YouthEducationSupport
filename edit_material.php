<?php

require_once __DIR__ . "/includes/auth.php";
include("db.php");

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$id=$_GET['id'];

$result=mysqli_query(
$conn,
"SELECT * FROM materials WHERE id='$id'"
);

$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

$title=$_POST['title'];
$subject=$_POST['subject'];
$description=$_POST['description'];
$link=$_POST['link'];

mysqli_query(
$conn,
"UPDATE materials
SET
title='$title',
subject='$subject',
description='$description',
link='$link'
WHERE id='$id'"
);

header("Location: manage_materials.php");

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Material</title>

<link rel="stylesheet" href="style.css?v=8">

</head>
<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>
<?php $pageHeroTitle = 'Edit Study Material'; $pageHeroText = 'Update the information and availability of a learning resource.'; $pageHeroImage = 'https://images.pexels.com/photos/6283211/pexels-photo-6283211.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>



<div class="container">

<form method="POST">

<h2>Edit Material</h2>

<input
type="text"
name="title"
value="<?php echo $row['title']; ?>"
required>

<input
type="text"
name="subject"
value="<?php echo $row['subject']; ?>"
required>

<textarea
name="description"
required><?php echo $row['description']; ?></textarea>

<input
type="text"
name="link"
value="<?php echo $row['link']; ?>">

<button
type="submit"
name="update">

Update Material

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>