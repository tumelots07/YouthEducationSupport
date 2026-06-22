<?php

session_start();
include("db.php");

$error="";

if(isset($_POST['login'])){

    $email=trim($_POST['email']);
    $password=trim($_POST['password']);

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){

        $error="Invalid email address.";

    }
    else{

        $sql="SELECT * FROM users
              WHERE email='$email'";

        $result=mysqli_query($conn,$sql);

        if($result && mysqli_num_rows($result)==1){

            $user=mysqli_fetch_assoc($result);

            if(password_verify($password,$user['password'])){

                $_SESSION['id']=$user['id'];
                $_SESSION['user']=$user['email'];
                $_SESSION['fullname']=$user['fullname'];
                $_SESSION['role']=$user['role'];

                if($user['role']=="admin"){

                    header("Location: admin_dashboard.php");

                }
                else{

                    header("Location: dashboard.php");

                }

                exit();

            }
            else{

                $error="Invalid email or password.";

            }

        }
        else{

            $error="Invalid email or password.";

        }

    }

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

<form method="POST">

<h2>Login</h2>

<?php

if($error!=""){

    echo "<div class='error'>$error</div>";

}

?>

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

<button
type="submit"
name="login">

Login

</button>

<p>

No account?

<a href="register.php">

Register

</a>

</p>

</form>

</div>

<script src="darkmode.js"></script>

</body>
</html>