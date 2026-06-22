<?php

session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){

    header("Location: login.php");
    exit();

}

$result = mysqli_query(

$conn,

"SELECT * FROM donations
ORDER BY created_at DESC"

);

if(!$result){

    die(mysqli_error($conn));

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Donations</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Donations</h1>

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
<th>Name</th>
<th>Email</th>
<th>Amount</th>
<th>Message</th>
<th>Date</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['fullname']; ?></td>

<td><?php echo $row['email']; ?></td>

<td>R<?php echo $row['amount']; ?></td>

<td><?php echo $row['message']; ?></td>

<td><?php echo $row['created_at']; ?></td>

</tr>

<?php

}

?>

</table>

</div>

<script src="darkmode.js"></script>

</body>
</html>
