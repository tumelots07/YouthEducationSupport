<?php

session_start();
include("db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

function highlight($text, $search){

    if($search==""){
        return $text;
    }

    return preg_replace(
        "/(" . preg_quote($search, "/") . ")/i",
        "<span class='highlight'>$1</span>",
        $text
    );

}

$search="";

if(isset($_GET['search'])){

    $search=mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $sql=
    "SELECT * FROM scholarships
    WHERE title LIKE '%$search%'
    OR provider LIKE '%$search%'
    OR deadline LIKE '%$search%'
    OR description LIKE '%$search%'";

}
else{

    $sql="SELECT * FROM scholarships";

}

$result=mysqli_query($conn,$sql);

if(!$result){

    die(mysqli_error($conn));

}

$count=mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html>
<head>

<title>Scholarships</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>Scholarships</h1>

<nav>

<a href="index.php">Home</a>
<a href="dashboard.php">Dashboard</a>
<a href="scholarships.php">Scholarships</a>
<a href="materials.php">Materials</a>
<a href="announcements.php">Announcements</a>
<a href="tutors.php">Tutors</a>
<a href="volunteer.php">Volunteer</a>
<a href="donations.php">Donations</a>
<a href="events.php">Events</a>
<a href="contact.php">Contact</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>

</nav>

</header>

<div class="container">

<form method="GET">

<input
type="text"
name="search"
placeholder="Search scholarships..."
value="<?php echo $search; ?>">

<button type="submit">

Search

</button>

</form>

<br>

<?php

if($search!=""){

?>

<h3>

<?php echo $count; ?>

result(s) found for

<span class="highlight">

<?php echo $search; ?>

</span>

</h3>

<?php

}

?>

<?php

if($count>0){

while($row=mysqli_fetch_assoc($result)){

?>

<div class="card">

<h3>

<?php echo highlight($row['title'],$search); ?>

</h3>

<p>

<b>Provider:</b>

<?php echo highlight($row['provider'],$search); ?>

</p>

<p>

<b>Deadline:</b>

<?php echo highlight($row['deadline'],$search); ?>

</p>

<p>

<?php echo highlight($row['description'],$search); ?>

</p>

<br>

<a href="apply_scholarship.php?id=<?php echo $row['id']; ?>">

<button>

Apply Now

</button>

</a>

</div>

<?php

}

}
else{

?>

<div class="card">

<h2>

No scholarships found.

</h2>

<p>

Try another keyword.

</p>

</div>

<?php

}

?>

</div>

<script src="darkmode.js"></script>

</body>
</html>
