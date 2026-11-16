<?php

require_once __DIR__ . "/includes/auth.php";
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

// Verified official destinations for well-known South African funding schemes.
// Other providers are not assigned guessed URLs.
function officialScholarshipUrl($title, $provider) {
    $text = strtolower($title . ' ' . $provider);
    if (strpos($text, 'nsfas') !== false || strpos($text, 'national student financial aid') !== false) {
        return ['url' => 'https://www.nsfas.org.za/content/how-to-apply.html', 'label' => 'Visit official NSFAS website'];
    }
    if (strpos($text, 'funza lushaka') !== false) {
        return ['url' => 'https://www.eservices.gov.za/FunzaLushaka/', 'label' => 'Visit Funza Lushaka application portal'];
    }
    return null;
}


?>

<!DOCTYPE html>
<html>
<head>

<title>Scholarships</title>

<link rel="stylesheet" href="style.css?v=8">

</head>

<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>


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
<?php $pageHeroTitle = 'Scholarship Opportunities'; $pageHeroText = 'Explore funding opportunities that can help you continue your studies.'; $pageHeroImage = 'https://images.pexels.com/photos/7713349/pexels-photo-7713349.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>


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

<div class="card scholarship-card">

<div class="scholarship-card-art">
<img src="https://images.pexels.com/photos/7713349/pexels-photo-7713349.jpeg?auto=compress&cs=tinysrgb&w=1400" alt="Scholarship funding illustration" loading="lazy">
<span>FUNDING OPPORTUNITY</span>
</div>

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

<?php
$officialLink = null;
if (!empty($row['application_url'])) {
    $savedUrl = trim($row['application_url']);
    if (filter_var($savedUrl, FILTER_VALIDATE_URL) &&
        in_array(strtolower((string)parse_url($savedUrl, PHP_URL_SCHEME)), ['http','https'], true)) {
        $officialLink = ['url' => $savedUrl];
    }
}
if (!$officialLink) $officialLink = officialScholarshipUrl($row['title'] ?? '', $row['provider'] ?? '');
?>
<?php if ($officialLink): ?>
<a class="scholarship-apply-button official-apply-button" href="<?php echo htmlspecialchars($officialLink['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
Apply Now <span aria-hidden="true">↗</span>
</a>
<?php else: ?>
<a class="scholarship-apply-button" href="apply_scholarship.php?id=<?php echo (int)$row['id']; ?>">
Apply Now <span aria-hidden="true">→</span>
</a>
<?php endif; ?>

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
