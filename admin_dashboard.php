<?php

session_start();

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

include("db.php");

/* Count records safely */

$users = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));

$scholarships = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM scholarships"));

$announcements = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM announcements"));

$events = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM events"));

$tutors = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM tutors"));

$messages = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM contacts"));

$bookings = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM tutor_bookings"));

$applications = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM scholarship_applications"));

$volunteers = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM volunteers"));

$donations = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM donations"));

?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Youth Education Support</h1>

<nav>

<a href="admin_dashboard.php">Dashboard</a>
<a href="manage_users.php">Users</a>
<a href="manage_scholarships.php">Scholarships</a>
<a href="manage_materials.php">Materials</a>
<a href="manage_announcements.php">Announcements</a>
<a href="manage_events.php">Events</a>
<a href="manage_tutors.php">Tutors</a>
<a href="manage_bookings.php">Bookings</a>
<a href="manage_applications.php">Applications</a>
<a href="manage_volunteers.php">Volunteers</a>
<a href="manage_donations.php">Donations</a>
<a href="manage_contacts.php">Messages</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>

</nav>

</header>

<div class="container">

<h2>

Welcome, <?php echo $_SESSION['fullname']; ?>

</h2>

<p>

Role: Administrator

</p>

<div class="stats">

<div class="stat-box" onclick="window.location='manage_users.php'">
<h2><?php echo $users; ?></h2>
<p>👥 Users</p>
</div>

<div class="stat-box" onclick="window.location='manage_scholarships.php'">
<h2><?php echo $scholarships; ?></h2>
<p>🎓 Scholarships</p>
</div>

<div class="stat-box" onclick="window.location='manage_announcements.php'">
<h2><?php echo $announcements; ?></h2>
<p>📢 Announcements</p>
</div>

<div class="stat-box" onclick="window.location='manage_events.php'">
<h2><?php echo $events; ?></h2>
<p>📅 Events</p>
</div>

<div class="stat-box" onclick="window.location='manage_tutors.php'">
<h2><?php echo $tutors; ?></h2>
<p>👨‍🏫 Tutors</p>
</div>

<div class="stat-box" onclick="window.location='manage_bookings.php'">
<h2><?php echo $bookings; ?></h2>
<p>📘 Bookings</p>
</div>

<div class="stat-box" onclick="window.location='manage_applications.php'">
<h2><?php echo $applications; ?></h2>
<p>📝 Applications</p>
</div>

<div class="stat-box" onclick="window.location='manage_volunteers.php'">
<h2><?php echo $volunteers; ?></h2>
<p>🤝 Volunteers</p>
</div>

<div class="stat-box" onclick="window.location='manage_donations.php'">
<h2><?php echo $donations; ?></h2>
<p>💰 Donations</p>
</div>

<div class="stat-box" onclick="window.location='manage_contacts.php'">
<h2><?php echo $messages; ?></h2>
<p>✉ Messages</p>
</div>

</div>

<br><br>

<h2>Quick Actions</h2>

<div class="stats">

<div class="stat-box"
onclick="window.location='manage_scholarships.php'">

<h3>🎓 Scholarships</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_announcements.php'">

<h3>📢 Announcements</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_events.php'">

<h3>📅 Events</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_tutors.php'">

<h3>👨‍🏫 Tutors</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_bookings.php'">

<h3>📘 Bookings</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_applications.php'">

<h3>📝 Applications</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_volunteers.php'">

<h3>🤝 Volunteers</h3>

</div>

<div class="stat-box"
onclick="window.location='manage_donations.php'">

<h3>💰 Donations</h3>

</div>

</div>

</div>

<footer>

<p>

Youth Education Support © 2026 | Administrator Panel

</p>

</footer>

<script src="darkmode.js"></script>

</body>
</html>
