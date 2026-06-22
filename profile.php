<?php

session_start();
include("db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email=$_SESSION['user'];

$message="";
$error="";

$result=mysqli_query(
$conn,
"SELECT * FROM users
WHERE email='$email'"
);

$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $fullname=trim($_POST['fullname']);
    $new_email=trim($_POST['email']);
    $new_password=trim($_POST['password']);

    // Check if email already exists
    $check=mysqli_query(
    $conn,
    "SELECT * FROM users
    WHERE email='$new_email'
    AND email<>'$email'"
    );

    if(mysqli_num_rows($check)>0){

        $error="Email already exists.";

    }
    else{

        if($new_password==""){

            mysqli_query(
            $conn,
            "UPDATE users
            SET
            fullname='$fullname',
            email='$new_email'
            WHERE email='$email'"
            );

        }
        else{

            $hashedPassword=password_hash(
            $new_password,
            PASSWORD_DEFAULT
            );

            mysqli_query(
            $conn,
            "UPDATE users
            SET
            fullname='$fullname',
            email='$new_email',
            password='$hashedPassword'
            WHERE email='$email'"
            );

        }

        $_SESSION['fullname']=$fullname;
        $_SESSION['user']=$new_email;

        $message="Profile updated successfully.";

        $email=$new_email;

        $result=mysqli_query(
        $conn,
        "SELECT * FROM users
        WHERE email='$email'"
        );

        $row=mysqli_fetch_assoc($result);

    }

}

?>

<!DOCTYPE html>
<html>
<head>

<title>My Profile</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>My Profile</h1>

<nav>

<?php

if($_SESSION['role']=="admin"){

?>

<a href="admin_dashboard.php">Dashboard</a>

<?php

}
else{

?>

<a href="dashboard.php">Dashboard</a>

<?php

}

?>

<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>

</nav>

</header>

<div class="container">

<form method="POST">

<h2>Profile Information</h2>

<?php

if($message!=""){
echo "<div class='success'>$message</div>";
}

if($error!=""){
echo "<div class='error'>$error</div>";
}

?>

<label>Full Name</label>

<input
type="text"
name="fullname"
value="<?php echo $row['fullname']; ?>"
required>

<label>Email Address</label>

<input
type="email"
name="email"
value="<?php echo $row['email']; ?>"
required>

<label>Role</label>

<input
type="text"
value="<?php echo ucfirst($row['role']); ?>"
readonly>

<label>
New Password
(Leave blank if you do not want to change it)
</label>

<input
type="password"
name="password"
placeholder="Enter new password">

<button
type="submit"
name="update">

Update Profile

</button>

</form>

</div>

<footer>

<p>

Youth Education Support © 2026

</p>

</footer>

<script src="darkmode.js"></script>

</body>
</html>