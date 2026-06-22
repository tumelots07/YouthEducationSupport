<?php

session_start();
include("db.php");

$id=$_GET['id'];

$result=mysqli_query($conn,
"SELECT * FROM scholarships
WHERE id='$id'");

$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

$title=$_POST['title'];
$provider=$_POST['provider'];
$deadline=$_POST['deadline'];
$description=$_POST['description'];

mysqli_query($conn,
"UPDATE scholarships
SET
title='$title',
provider='$provider',
deadline='$deadline',
description='$description'
WHERE id='$id'");

header("Location: manage_scholarships.php");

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Scholarship</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

<form method="POST">

<h2>Edit Scholarship</h2>

<input
type="text"
name="title"
value="<?php echo $row['title']; ?>"
required>

<input
type="text"
name="provider"
value="<?php echo $row['provider']; ?>"
required>

<input
type="date"
name="deadline"
value="<?php echo $row['deadline']; ?>"
required>

<textarea
name="description"
style="width:100%;height:120px;"
required><?php echo $row['description']; ?></textarea>

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