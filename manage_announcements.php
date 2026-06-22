<?php

session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

    $title=$_POST['title'];
    $message=$_POST['message'];

    mysqli_query(
        $conn,
        "INSERT INTO announcements(title,message)
        VALUES('$title','$message')"
    );
}

$result=mysqli_query(
$conn,
"SELECT * FROM announcements ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Announcements</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>

<h1>Announcements</h1>

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

<form method="POST">

<h2>Add Announcement</h2>

<input
type="text"
name="title"
placeholder="Title"
required>

<textarea
name="message"
style="width:100%;height:120px;"
required></textarea>

<button
type="submit"
name="add">

Add Announcement

</button>

</form>

<br>

<div class="card">

<h2>All Announcements</h2>

<table>

<tr>

<th>ID</th>
<th>Title</th>
<th>Date</th>
<th>Actions</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>

<a href="edit_announcement.php?id=<?php echo $row['id']; ?>">
Edit
</a>

|

<a href="delete_announcement.php?id=<?php echo $row['id']; ?>">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script src="darkmode.js"></script>

</body>
</html>