<?php

session_start();
include("db.php");

$id=$_GET['id'];

$result=mysqli_query(
$conn,
"SELECT * FROM tutors
WHERE id='$id'");

$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $tutor_name=$_POST['tutor_name'];
    $subject=$_POST['subject'];
    $email=$_POST['email'];

    mysqli_query(
    $conn,
    "UPDATE tutors
    SET
    tutor_name='$tutor_name',
    subject='$subject',
    email='$email'
    WHERE id='$id'"
    );

    header("Location: manage_tutors.php");

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Tutor</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<form method="POST">

<h2>Edit Tutor</h2>

<input
type="text"
name="tutor_name"
value="<?php echo $row['tutor_name']; ?>"
required>

<input
type="text"
name="subject"
value="<?php echo $row['subject']; ?>"
required>

<input
type="email"
name="email"
value="<?php echo $row['email']; ?>"
required>

<button
type="submit"
name="update">

Update Tutor

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>