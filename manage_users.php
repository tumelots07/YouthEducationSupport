<?php

session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$search="";

if(isset($_GET['search'])){
    $search=$_GET['search'];

    $result=mysqli_query($conn,
    "SELECT * FROM users
    WHERE fullname LIKE '%$search%'
    OR email LIKE '%$search%'");
}
else{
    $result=mysqli_query($conn,
    "SELECT * FROM users");
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Users</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Users</h1>

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

<div class="card">

<h2>Manage Users</h2>

<form method="GET">

<input
type="text"
name="search"
placeholder="Search users..."
value="<?php echo $search; ?>">

<button type="submit">
Search
</button>

</form>

</div>

<div class="card">

<table border="1" width="100%" cellpadding="10">

<tr>

<th>ID</th>
<th>Full Name</th>
<th>Email</th>
<th>Role</th>
<th>Created</th>
<th>Actions</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['fullname']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['role']; ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>

<a href="edit_user.php?id=<?php echo $row['id']; ?>">
Edit
</a>

|

<a href="delete_user.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this user?')">
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