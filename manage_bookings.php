<?php

session_start();
include("db.php");

if($_SESSION['role']!="admin"){

    header("Location: login.php");
    exit();

}

$result=mysqli_query(
$conn,
"SELECT * FROM tutor_bookings
ORDER BY booking_date DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Tutor Bookings</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Tutor Bookings</h1>

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

<table border="1">

<tr>

<th>ID</th>
<th>Student</th>
<th>Email</th>
<th>Tutor</th>
<th>Subject</th>
<th>Date</th>
<th>Session Date</th>
<th>Time</th>
<th>Notes</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['student_name']; ?></td>

<td><?php echo $row['student_email']; ?></td>

<td><?php echo $row['tutor_name']; ?></td>

<td><?php echo $row['subject']; ?></td>

<td><?php echo $row['booking_date']; ?></td>

<td><?php echo $row['session_date']; ?></td>

<td><?php echo $row['session_time']; ?></td>

<td><?php echo $row['notes']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<script src="darkmode.js"></script>

</body>
</html>
