<?php

require_once __DIR__ . "/includes/auth.php";
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

<link rel="stylesheet" href="style.css?v=8">

</head>

<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>
<?php $pageHeroTitle = 'Scholarship Application'; $pageHeroText = 'Complete the next step towards an educational funding opportunity.'; $pageHeroImage = 'https://images.pexels.com/photos/7713349/pexels-photo-7713349.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>



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
