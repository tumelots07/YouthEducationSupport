<?php

session_start();
include("db.php");

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$id=$_GET['id'];

$result=mysqli_query($conn,
"SELECT * FROM users
WHERE id='$id'");

$user=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

$fullname=$_POST['fullname'];
$email=$_POST['email'];
$role=$_POST['role'];

mysqli_query($conn,
"UPDATE users
SET
fullname='$fullname',
email='$email',
role='$role'
WHERE id='$id'");

header("Location: manage_users.php");
exit();

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit User</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

<form method="POST">

<h2>Edit User</h2>

<input
type="text"
name="fullname"
value="<?php echo $user['fullname']; ?>"
required>

<input
type="email"
name="email"
value="<?php echo $user['email']; ?>"
required>

<select
name="role"
required
style="width:100%;padding:12px;margin-top:15px;">

<option value="student"
<?php if($user['role']=="student") echo "selected"; ?>>
Student
</option>

<option value="admin"
<?php if($user['role']=="admin") echo "selected"; ?>>
Admin
</option>

</select>

<button
type="submit"
name="update">

Update User

</button>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>