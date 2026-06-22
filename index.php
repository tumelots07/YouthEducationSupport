<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>

<title>Youth Education Support</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Youth Education Support</h1>

<nav>

<a href="index.php">Home</a>

<?php if(isset($_SESSION['user'])){ ?>

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

<?php } else { ?>

<a href="login.php">Login</a>
<a href="register.php">Register</a>

<?php } ?>

</nav>

</header>

<section class="hero">

<h2>Empowering Students Through Education</h2>

<p>

Youth Education Support provides students with access to
scholarships, announcements, tutoring services,
study materials and educational events.

</p>

<br>

<?php if(!isset($_SESSION['user'])){ ?>

<a href="register.php">

<button>

Get Started

</button>

</a>

<?php } ?>

<br><br>

<button onclick="toggleDarkMode()">

🌙 Toggle Dark Mode

</button>

</section>

<div class="container">

<h2 class="section-title">

Our Services

</h2>

<div class="stats">

<div class="card">

<h3>🎓 Scholarships</h3>

<p>

Discover opportunities that can help fund your education.

</p>

</div>

<div class="card">

<h3>📢 Announcements</h3>

<p>

Stay informed about important updates and notices.

</p>

</div>

<div class="card">

<h3>👨‍🏫 Tutors</h3>

<p>

Find academic support from qualified tutors.

</p>

</div>

<div class="card">

<h3>📅 Events</h3>

<p>

Participate in educational workshops and activities.

</p>

</div>

</div>

<br><br>

<div class="card">

<h2>About the System</h2>

<p>

Youth Education Support is a web-based platform designed
to help students access educational opportunities and
support services through a simple and user-friendly interface.

</p>

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