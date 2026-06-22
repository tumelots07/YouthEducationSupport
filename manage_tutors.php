<?php

session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

    $tutor_name=$_POST['tutor_name'];
    $subject=$_POST['subject'];
    $email=$_POST['email'];

    mysqli_query(
    $conn,
    "INSERT INTO tutors(tutor_name,subject,email)
    VALUES('$tutor_name','$subject','$email')"
    );
}

$result=mysqli_query(
$conn,
"SELECT * FROM tutors");

?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Tutors</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>

<h1>Tutors</h1>

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

<h2>Add Tutor</h2>

<input
type="text"
name="tutor_name"
placeholder="Tutor Name"
required>

<input
type="text"
name="subject"
placeholder="Subject"
required>

<input
type="email"
name="email"
placeholder="Email"
required>

<button
type="submit"
name="add">

Add Tutor

</button>

</form>

<br>

<div class="card">

<h2>All Tutors</h2>

<table>

<tr>

<th>ID</th>
<th>Name</th>
<th>Subject</th>
<th>Email</th>
<th>Actions</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['tutor_name']; ?></td>

<td><?php echo $row['subject']; ?></td>

<td><?php echo $row['email']; ?></td>

<td>

<a href="edit_tutor.php?id=<?php echo $row['id']; ?>">
Edit
</a>

|

<a
onclick="return confirm('Delete this tutor?')"
href="delete_tutor.php?id=<?php echo $row['id']; ?>">
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