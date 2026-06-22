<?php

session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

$title=$_POST['title'];
$provider=$_POST['provider'];
$deadline=$_POST['deadline'];
$description=$_POST['description'];

mysqli_query($conn,
"INSERT INTO scholarships
(title,provider,deadline,description)
VALUES
('$title','$provider','$deadline','$description')");
}

$result=mysqli_query($conn,
"SELECT * FROM scholarships");

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Scholarships</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Scholarships</h1>

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

<h2>Add Scholarship</h2>

<input
type="text"
name="title"
placeholder="Scholarship Title"
required>

<input
type="text"
name="provider"
placeholder="Provider"
required>

<input
type="date"
name="deadline"
required>

<textarea
name="description"
placeholder="Description"
required
style="width:100%;height:120px;"></textarea>

<button
type="submit"
name="add">

Add Scholarship

</button>

</form>

<br>

<div class="card">

<h2>All Scholarships</h2>

<table border="1" width="100%" cellpadding="10">

<tr>

<th>ID</th>
<th>Title</th>
<th>Provider</th>
<th>Deadline</th>
<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['provider']; ?></td>

<td><?php echo $row['deadline']; ?></td>

<td>

<a href="delete_scholarship.php?id=<?php echo $row['id']; ?>">

Delete

</a>

|

<a href="edit_scholarship.php?id=<?php echo $row['id']; ?>">

Edit

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