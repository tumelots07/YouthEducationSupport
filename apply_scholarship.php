<?php

session_start();
include("db.php");

if(!isset($_SESSION['user'])){

    header("Location: login.php");
    exit();

}

$id = $_GET['id'];

$result = mysqli_query(
$conn,
"SELECT * FROM scholarships WHERE id='$id'"
);

if(mysqli_num_rows($result)==0){

    die("Scholarship not found.");

}

$row = mysqli_fetch_assoc($result);

$title = $row['title'];

$name = $_SESSION['fullname'];

$email = $_SESSION['user'];

mysqli_query(

$conn,

"INSERT INTO scholarship_applications
(student_name,student_email,scholarship_title)

VALUES

('$name','$email','$title')"

);

?>

<!DOCTYPE html>
<html>
<head>

<title>Application Successful</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

<div class="success">

<h2>

Application Submitted Successfully!

</h2>

<p>

You have successfully applied for:

</p>

<h3>

<?php echo $title; ?>

</h3>

<br>

<a href="scholarships.php">

<button>

Back to Scholarships

</button>

</a>

</div>

</div>

<script src="darkmode.js"></script>

</body>
</html>
