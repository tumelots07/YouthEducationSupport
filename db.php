<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "youth_education_support";

$conn = mysqli_connect(
    $servername,
    $username,
    $password,
    $database
);

if(!$conn){
    die("Connection failed: "
    . mysqli_connect_error());
}

?>