<?php

session_start();
include("db.php");

// Check if user is logged in and is admin
if(!isset($_SESSION['user']) || $_SESSION['role'] != "admin"){
    header("Location: login.php");
    exit();
}

// Get ID from URL
$id = $_GET['id'];

// Prevent admin from deleting themselves
if($id != $_SESSION['id']){

    mysqli_query(
        $conn,
        "DELETE FROM users WHERE id='$id'"
    );

}

header("Location: manage_users.php");
exit();

?>