<?php

require_once __DIR__ . "/includes/auth.php";
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

<link rel="stylesheet" href="style.css?v=8">

</head>

<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>


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
<?php $pageHeroTitle = 'Latest Announcements'; $pageHeroText = 'Keep up with news, updates and important information.'; $pageHeroImage = 'https://images.pexels.com/photos/5940827/pexels-photo-5940827.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>


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