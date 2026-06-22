<?php

session_start();
include("db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$result=mysqli_query(
$conn,
"SELECT * FROM materials"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Study Materials</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Study Material</h1>

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

<?php

while($row=mysqli_fetch_assoc($result)){

?>

<div class="card">

<h2>

<?php echo $row['title']; ?>

</h2>

<p>

<b>Subject:</b>

<?php echo $row['subject']; ?>

</p>

<p>

<?php echo $row['description']; ?>

</p>

<?php

if($row['link']!=""){

?>

<a
href="<?php echo $row['link']; ?>"
target="_blank">

<button>

Open Material

</button>

</a>

<?php

}

?>

</div>

<br>

<?php

}

?>

</div>

<script src="darkmode.js"></script>

</body>
</html>