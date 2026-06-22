<?php

session_start();
include("db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$result = mysqli_query(
$conn,
"SELECT * FROM tutors"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Tutors</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Tutors</h1>

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

<div class="container">

<h1>Available Tutors</h1>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<div class="card">

<h2>

<?php echo $row['tutor_name']; ?>

</h2>

<p>

<b>Subject:</b>

<?php echo $row['subject']; ?>

</p>

<p>

<b>Email:</b>

<?php echo $row['email']; ?>

</p>

<br>

<a href="book_tutor.php?id=<?php echo $row['id']; ?>">

<button>

Book Session

</button>

</a>

</div>

<br>

<?php

}

}
else{

?>

<div class="card">

<h2>

No tutors available.

</h2>

<p>

Please check again later.

</p>

</div>

<?php

}

?>

</div>

<script src="darkmode.js"></script>

</body>
</html>
