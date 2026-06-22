<?php

session_start();

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(
isset($_SESSION['LAST_ACTIVITY']) &&
(time()-$_SESSION['LAST_ACTIVITY']>1800)
){
    session_unset();
    session_destroy();

    header("Location: login.php");
    exit();
}

$_SESSION['LAST_ACTIVITY']=time();

include("db.php");

/* Count records safely */

$scholarships_result=mysqli_query(
$conn,
"SELECT * FROM scholarships"
);

$scholarships=($scholarships_result)?
mysqli_num_rows($scholarships_result):0;


$materials_result=mysqli_query(
$conn,
"SELECT * FROM materials"
);

$materials=($materials_result)?
mysqli_num_rows($materials_result):0;


$events_result=mysqli_query(
$conn,
"SELECT * FROM events"
);

$events=($events_result)?
mysqli_num_rows($events_result):0;


$tutors_result=mysqli_query(
$conn,
"SELECT * FROM tutors"
);

$tutors=($tutors_result)?
mysqli_num_rows($tutors_result):0;

?>

<!DOCTYPE html>
<html>
<head>

<title>Student Dashboard</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Youth Education Support</h1>

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

<h2>

Welcome,

<?php echo $_SESSION['fullname']; ?>

</h2>

<p>

You are logged in as a Student.

</p>

<h2>System Statistics</h2>

<div class="stats">

<div class="stat-box"
onclick="window.location='scholarships.php'">

<h2>

<?php echo $scholarships; ?>

</h2>

<p>

🎓 Scholarships

</p>

</div>

<div class="stat-box"
onclick="window.location='materials.php'">

<h2>

<?php echo $materials; ?>

</h2>

<p>

📚 Materials

</p>

</div>

<div class="stat-box"
onclick="window.location='events.php'">

<h2>

<?php echo $events; ?>

</h2>

<p>

📅 Events

</p>

</div>

<div class="stat-box"
onclick="window.location='tutors.php'">

<h2>

<?php echo $tutors; ?>

</h2>

<p>

👨‍🏫 Tutors

</p>

</div>

</div>

<br>

<h2>Quick Access</h2>

<div class="stats">

<div class="card"
onclick="window.location.href='scholarships.php'">

<h3>🎓 Scholarships</h3>

<p>
View available opportunities.
</p>

</div>

<div class="card"
onclick="window.location.href='materials.php'">

<h3>📚 Study Materials</h3>

<p>
Access learning resources and notes.
</p>

</div>

<div class="card"
onclick="window.location.href='announcements.php'">

<h3>📢 Announcements</h3>

<p>
Stay updated with latest news.
</p>

</div>

<div class="card"
onclick="window.location.href='events.php'">

<h3>📅 Events</h3>

<p>
View upcoming educational events.
</p>

</div>

<div class="card"
onclick="window.location.href='tutors.php'">

<h3>👨‍🏫 Tutors</h3>

<p>
Find academic support and tutoring.
</p>

</div>

<div class="card"
onclick="window.location.href='volunteer.php'">

<h3>🤝 Volunteer</h3>

<p>
Join and help other students.
</p>

</div>

<div class="card"
onclick="window.location.href='donations.php'">

<h3>💰 Donations</h3>

<p>
Support educational initiatives.
</p>

</div>

<div class="card"
onclick="window.location.href='profile.php'">

<h3>👤 Profile</h3>

<p>
Manage your account information.
</p>

</div>

</div>

</div>

<footer>

<p>

Youth Education Support © 2026

</p>

</footer>

<script src="darkmode.js"></script>

</body>
</html>
