<?php

session_start();
include("db.php");

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$result=mysqli_query($conn,"SELECT * FROM materials");

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Materials</title>

<link rel="stylesheet" href="style.css">

</head>
<body>

<header>

<h1>Materials</h1>

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

<h2>Manage Study Materials</h2>

<a href="add_material.php">

<button>

Add Material

</button>

</a>

<br><br>

<table border="1">

<tr>

<th>ID</th>
<th>Title</th>
<th>Subject</th>
<th>Actions</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['subject']; ?></td>

<td>

<a href="edit_material.php?id=<?php echo $row['id']; ?>">

Edit

</a>

|

<a
href="delete_material.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this material?')">

Delete

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

<script src="darkmode.js"></script>

</body>
</html>