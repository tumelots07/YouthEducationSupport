<?php

session_start();
include("db.php");

$message="";

if(isset($_POST['send'])){

$name=$_POST['name'];
$email=$_POST['email'];
$msg=$_POST['message'];

mysqli_query(
$conn,
"INSERT INTO contacts(name,email,message)
VALUES('$name','$email','$msg')");

$message="Message sent successfully.";

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Contact</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Contact Us</h1>

<nav>

<a href="index.php">Home</a>
<a href="dashboard.php">Dashboard</a>
<a href="scholarships.php">Scholarships</a>
<a href="materials.php">Materials</a>
<a href="announcements.php">Announcements</a>
<a href="tutors.php">Tutors</a>
<a href="volunteer.php">Volunteer</a>
<a href="donations.php">Donations</a>
<a href="events.php">Events</a>
<a href="contact.php">Contact</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>

</nav>

</header>

<div class="container">

<form method="POST">

<h2>Send Message</h2>

<?php

if($message!=""){
echo "<div class='success'>$message</div>";
}

?>

<input
type="text"
name="name"
placeholder="Name"
required>

<input
type="email"
name="email"
placeholder="Email"
required>

<textarea
name="message"
style="width:100%;height:120px;"
required></textarea>

<button
type="submit"
name="send">

Send Message

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>