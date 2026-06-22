<?php

session_start();
include("db.php");

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

$title=$_POST['title'];
$subject=$_POST['subject'];
$description=$_POST['description'];
$link=$_POST['link'];

mysqli_query(
$conn,
"INSERT INTO materials(title,subject,description,link)
VALUES('$title','$subject','$description','$link')"
);

header("Location: manage_materials.php");

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Add Material</title>

<link rel="stylesheet" href="style.css">

</head>
<body>

<div class="container">

<form method="POST">

<h2>Add Study Material</h2>

<input
type="text"
name="title"
placeholder="Title"
required>

<input
type="text"
name="subject"
placeholder="Subject"
required>

<textarea
name="description"
placeholder="Description"
required></textarea>

<input
type="text"
name="link"
placeholder="Website Link">

<button
type="submit"
name="add">

Add Material

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>