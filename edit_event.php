<?php

session_start();
include("db.php");

$id=$_GET['id'];

$result=mysqli_query(
$conn,
"SELECT * FROM events
WHERE id='$id'");

$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $title=$_POST['title'];
    $date=$_POST['date'];
    $venue=$_POST['venue'];
    $description=$_POST['description'];

    mysqli_query(
        $conn,
        "UPDATE events
        SET
        title='$title',
        date='$date',
        venue='$venue',
        description='$description'
        WHERE id='$id'"
    );

    header("Location: manage_events.php");
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Event</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<form method="POST">

<h2>Edit Event</h2>

<input
type="text"
name="title"
value="<?php echo $row['title']; ?>"
required>

<input
type="date"
name="date"
value="<?php echo $row['date']; ?>"
required>

<input
type="text"
name="venue"
value="<?php echo $row['venue']; ?>"
required>

<textarea
name="description"
style="width:100%;height:120px;"
required><?php echo $row['description']; ?></textarea>

<button
type="submit"
name="update">

Update Event

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>