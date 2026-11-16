<?php

require_once __DIR__ . "/includes/auth.php";
include("db.php");

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$id=$_GET['id'];

mysqli_query(
$conn,
"DELETE FROM materials
WHERE id='$id'"
);

header("Location: manage_materials.php");

?>