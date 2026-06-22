<?php

include("db.php");

$message="";

if(isset($_POST['register'])){

    $fullname=trim($_POST['fullname']);
    $email=trim($_POST['email']);
    $password=trim($_POST['password']);
    $role=$_POST['role'];
    $admin_code=trim($_POST['admin_code']);

    if(strlen($fullname)<3){

        $message="Full name must be at least 3 characters.";

    }
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){

        $message="Invalid email address.";

    }
    elseif(
        strlen($password)<8 ||
        !preg_match("/[A-Z]/",$password) ||
        !preg_match("/[0-9]/",$password)
    ){

        $message=
        "Password must contain at least 8 characters, one uppercase letter and one number.";

    }
    elseif($role=="admin" && $admin_code!="YES2026"){

        $message="Invalid admin code.";

    }
    else{

        $check=mysqli_query(
        $conn,
        "SELECT * FROM users
        WHERE email='$email'"
        );

        if(mysqli_num_rows($check)>0){

            $message="Email already exists.";

        }
        else{

            $hashedPassword=password_hash(
            $password,
            PASSWORD_DEFAULT
            );

            $sql=
            "INSERT INTO users
            (fullname,email,password,role)
            VALUES
            ('$fullname','$email','$hashedPassword','$role')";

            if(mysqli_query($conn,$sql)){

                header("Location: login.php");
                exit();

            }
            else{

                $message="Registration failed.";

            }

        }

    }

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Register</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

<form method="POST">

<h2>Register</h2>

<?php

if($message!=""){

    echo "<div class='error'>$message</div>";

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
type="password"
name="password"
placeholder="Password"
required>

<label>Role</label>

<select
name="role"
required>

<option value="student">

Student

</option>

<option value="admin">

Admin

</option>

</select>

<input
type="text"
name="admin_code"
placeholder="Admin Code (Admins only)">

<button
type="submit"
name="register">

Register

</button>

<p>

Already have an account?

<a href="login.php">

Login

</a>

</p>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>