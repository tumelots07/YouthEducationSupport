<?php

require_once __DIR__ . "/includes/auth.php";
include("db.php");

$result=mysqli_query(
$conn,
"SELECT * FROM events
ORDER BY date ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events | Youth Education Support</title>
<link rel="stylesheet" href="style.css?v=8">
</head>
<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>
<header>
<h1>Events</h1>
<nav>
<a href="index.php">Home</a><a href="dashboard.php">Dashboard</a><a href="scholarships.php">Scholarships</a>
<a href="materials.php">Materials</a><a href="announcements.php">Announcements</a><a href="tutors.php">Tutors</a>
<a href="volunteer.php">Volunteer</a><a href="donations.php">Donations</a><a href="events.php">Events</a>
<a href="contact.php">Contact</a><a href="profile.php">Profile</a><a href="logout.php">Logout</a>
</nav>
</header>
<?php $pageHeroTitle = 'Upcoming Events'; $pageHeroText = 'Discover workshops, campus activities and educational events.'; $pageHeroImage = 'https://images.pexels.com/photos/5940827/pexels-photo-5940827.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>

<main class="container events-page">
<div class="events-intro">
 <div><span class="section-kicker">LEARN • CONNECT • GROW</span><h1>Upcoming educational events</h1>
 <p>Explore workshops, information sessions and activities that support your academic journey.</p></div>
 <img src="assets/illustrations/campus-workshop.svg" alt="Students taking part in an educational workshop" loading="lazy">
</div>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<div class="event-poster-grid">
<?php while($row=mysqli_fetch_assoc($result)):
 $eventTitle = (string)($row['title'] ?? 'Youth Education Event');
 $eventText = strtolower($eventTitle . ' ' . (string)($row['description'] ?? ''));
 if (strpos($eventText, 'prayer') !== false || strpos($eventText, 'worship') !== false || strpos($eventText, 'faith') !== false) {
     $posterPhoto = 'https://images.pexels.com/photos/13899667/pexels-photo-13899667.jpeg?auto=compress&cs=tinysrgb&w=1400';
     $posterAlt = 'People gathered together in a communal prayer session';
 } elseif (strpos($eventText, 'tutor') !== false || strpos($eventText, 'mentor') !== false) {
     $posterPhoto = 'https://images.pexels.com/photos/5428011/pexels-photo-5428011.jpeg?auto=compress&cs=tinysrgb&w=1400';
     $posterAlt = 'A tutor helping a learner with schoolwork';
 } elseif (strpos($eventText, 'career') !== false || strpos($eventText, 'graduat') !== false) {
     $posterPhoto = 'https://images.pexels.com/photos/7713349/pexels-photo-7713349.jpeg?auto=compress&cs=tinysrgb&w=1400';
     $posterAlt = 'Graduates celebrating their academic achievement';
 } elseif (strpos($eventText, 'volunteer') !== false || strpos($eventText, 'community') !== false) {
     $posterPhoto = 'https://images.pexels.com/photos/6503098/pexels-photo-6503098.jpeg?auto=compress&cs=tinysrgb&w=1400';
     $posterAlt = 'A volunteer helping a learner';
 } elseif (strpos($eventText, 'study') !== false || strpos($eventText, 'workshop') !== false || strpos($eventText, 'seminar') !== false) {
     $posterPhoto = 'https://images.pexels.com/photos/5940827/pexels-photo-5940827.jpeg?auto=compress&cs=tinysrgb&w=1400';
     $posterAlt = 'People attending an educational workshop';
 } else {
     $posterPhoto = 'https://images.pexels.com/photos/6283211/pexels-photo-6283211.jpeg?auto=compress&cs=tinysrgb&w=1400';
     $posterAlt = 'Students working together in a study session';
 }
?>
<article class="event-poster-card">
 <div class="event-poster">
  <img src="<?php echo htmlspecialchars($posterPhoto, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($posterAlt, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
  <span class="event-poster-label">YOUTH EDUCATION SUPPORT</span>
  <h2><?php echo htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
  <div class="poster-bottom"><span>LEARN</span><span>CONNECT</span><span>GROW</span></div>
 </div>
 <div class="event-poster-details">
  <h3><?php echo htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
  <p><strong>Date:</strong> <?php echo htmlspecialchars((string)($row['date'] ?? 'To be confirmed'), ENT_QUOTES, 'UTF-8'); ?></p>
  <p><strong>Venue:</strong> <?php echo htmlspecialchars((string)($row['venue'] ?? 'To be confirmed'), ENT_QUOTES, 'UTF-8'); ?></p>
  <p><?php echo nl2br(htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></p>
 </div>
</article>
<?php endwhile; ?>
</div>
<?php else: ?>
<div class="card empty-events"><img src="assets/illustrations/campus-workshop.svg" alt="" loading="lazy"><h2>No events are currently listed</h2><p>Please check again later for upcoming workshops and activities.</p></div>
<?php endif; ?>
</main>
<script src="darkmode.js"></script>
</body>
</html>
