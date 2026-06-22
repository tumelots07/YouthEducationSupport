<?php

session_start();
include("db.php");

$result=mysqli_query(
$conn,
"SELECT * FROM announcements
ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html>
<head>

<title>Announcements</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Announcements</h1>

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

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="card">

<h2>
<?php echo $row['title']; ?>
</h2>

<p>
<?php echo $row['message']; ?>
</p>

<p>
<b>Date:</b>
<?php echo $row['created_at']; ?>
</p>

</div>

<?php } ?>

</div>

<script src="darkmode.js"></script>

</body>
</html>