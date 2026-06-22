<?php

session_start();
include("db.php");

$id=$_GET['id'];

mysqli_query(
$conn,
"DELETE FROM announcements
WHERE id='$id'");

header("Location: manage_announcements.php");

?>