<?php

require_once __DIR__ . "/includes/auth.php";

include("db.php");

$success="";

if(isset($_POST['donate'])){

    $fullname=$_POST['fullname'];
    $email=$_POST['email'];
    $amount=$_POST['amount'];
    $message=$_POST['message'];

    mysqli_query(

    $conn,

    "INSERT INTO donations
    (fullname,email,amount,message)

    VALUES

    ('$fullname','$email','$amount','$message')"

    );

    $success="Thank you for your donation!";

}

?>

<!DOCTYPE html>

<html>
<head>

<title>Donations</title>

<link rel="stylesheet" href="style.css?v=8">

</head>

<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>


<header>

<h1>Support Our Mission</h1>

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
<?php $pageHeroTitle = 'Support Our Mission'; $pageHeroText = 'Help create more educational opportunities for young people.'; $pageHeroImage = 'https://images.pexels.com/photos/7713349/pexels-photo-7713349.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>


<div class="container">

<form method="POST">

<h2>

Make a Donation

</h2>

<?php

if($success!=""){

echo "<div class='success'>$success</div>";

}

?>

<input
type="text"
name="fullname"
placeholder="Full Name"
required>

<input
type="email"
name="email"
placeholder="Email Address"
required>

<input
type="number"
name="amount"
placeholder="Amount"
step="0.01"
required>

<textarea
name="message"
rows="5"
placeholder="Message (optional)"></textarea>

<button
type="submit"
name="donate">

Donate

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>
