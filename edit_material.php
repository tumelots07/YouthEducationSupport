<?php

session_start();
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

<link rel="stylesheet" href="style.css">

</head>
<body>

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