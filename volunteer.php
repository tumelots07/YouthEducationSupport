<?php

session_start();

include("db.php");

$message="";

if(isset($_POST['submit'])){

    $fullname=$_POST['fullname'];
    $email=$_POST['email'];
    $skills=$_POST['skills'];

    mysqli_query(

    $conn,

    "INSERT INTO volunteers
    (fullname,email,skills)

    VALUES

    ('$fullname','$email','$skills')"

    );

    $message="Thank you for volunteering!";

}

?>

<!DOCTYPE html>

<html>
<head>

<title>Volunteer</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Volunteer With Us</h1>

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

<h2>

Become a Volunteer

</h2>

<?php

if($message!=""){

echo "<div class='success'>$message</div>";

}

?>

<input
type="text"
name="fullname"
placeholder="Full Name"
required>

<input
type="email"
name="email"
placeholder="Email Address"
required>

<textarea
name="skills"
rows="5"
placeholder="Tell us about your skills..."
required></textarea>

<button
type="submit"
name="submit">

Volunteer

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>
