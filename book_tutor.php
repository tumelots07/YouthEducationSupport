<?php

session_start();
include("db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$id=$_GET['id'];

$result=mysqli_query(
$conn,
"SELECT * FROM tutors WHERE id='$id'"
);

$row=mysqli_fetch_assoc($result);

$tutor_name=$row['tutor_name'];
$subject=$row['subject'];

$message="";

if(isset($_POST['book'])){

    $session_date=$_POST['session_date'];
    $session_time=$_POST['session_time'];
    $notes=$_POST['notes'];

    $student_name=$_SESSION['fullname'];
    $student_email=$_SESSION['user'];

    mysqli_query(
$conn,

"INSERT INTO tutor_bookings
(student_name,student_email,tutor_name,subject,
session_date,session_time,notes)

VALUES

('$student_name',
'$student_email',
'$tutor_name',
'$subject',
'$session_date',
'$session_time',
'$notes')"
);

$message="Session booked successfully! Redirecting to Tutors page...";

header("Refresh:2; url=tutors.php");

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Book Tutor Session</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Book Tutor Session</h1>

</header>

<div class="container">

<form method="POST">

<h2>

<?php echo $tutor_name; ?>

</h2>

<p>

Subject:

<b>

<?php echo $subject; ?>

</b>

</p>

<?php

if($message!=""){

echo "<div class='success'>$message</div>";

}

?>

<label>Select Date</label>

<input
type="date"
name="session_date"
required>

<label>Select Time</label>

<input
type="time"
name="session_time"
required>

<label>Additional Notes</label>

<textarea
name="notes"
rows="4"></textarea>

<button
type="submit"
name="book">

Confirm Booking

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>
