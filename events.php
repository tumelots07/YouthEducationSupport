<?php

session_start();
include("db.php");

$result=mysqli_query(
$conn,
"SELECT * FROM events
ORDER BY date ASC");

?>

<!DOCTYPE html>
<html>
<head>
<title>Events</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>

<h1>Events</h1>

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

<h1>Upcoming Events</h1>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="card">

<h2>
<?php echo $row['title']; ?>
</h2>

<p>

<b>Date:</b>
<?php echo $row['date']; ?>

</p>

<p>

<b>Venue:</b>
<?php echo $row['venue']; ?>

</p>

<p>

<?php echo $row['description']; ?>

</p>

</div>

<br>

<?php } ?>

</div>

<script src="darkmode.js"></script>

</body>
</html>