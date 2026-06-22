<?php

session_start();
include("db.php");

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$id=$_GET['id'];

mysqli_query($conn,
"DELETE FROM scholarships
WHERE id='$id'");

header("Location: manage_scholarships.php");

?>