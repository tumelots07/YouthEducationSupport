<?php
require_once __DIR__ . "/includes/auth.php";
auth_require_role('student');
require_once __DIR__ . "/db.php";

function yesCount($conn, $table) {
    $allowed = ['scholarships','materials','events','tutors'];
    if (!in_array($table, $allowed, true)) return 0;
    $q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$table`");
    if (!$q) return 0;
    $r = mysqli_fetch_assoc($q);
    return (int)($r['total'] ?? 0);
}
$scholarships = yesCount($conn, 'scholarships');
$materials = yesCount($conn, 'materials');
$events = yesCount($conn, 'events');
$tutors = yesCount($conn, 'tutors');
$fullname = htmlspecialchars($_SESSION['fullname'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard | Youth Education Support</title>
<link rel="icon" type="image/svg+xml" href="assets/yes-logo.svg">
<link rel="stylesheet" href="style.css?v=8">
</head>
<body class="dashboard-layout has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>


<main>
<section class="dashboard-welcome"><div class="dashboard-welcome-inner">
<div><span class="section-kicker">STUDENT DASHBOARD</span><h1>Welcome back, <?= $fullname ?>!</h1><p>Your learning journey starts here. Explore scholarships, study materials, tutoring and educational opportunities.</p></div>
<img src="https://psu-gatsby-files-prod.s3.amazonaws.com/s3fs-public/styles/4_3_1500w/public/2024/10/penn-state-learning-2023.jpg?h=427191ee&itok=82NBoRSZ" alt="Illustration of a student planning their studies">
</div></section>
<section class="section dashboard-section">
<div class="section-heading"><span class="section-kicker">YOUR LEARNING SPACE</span><h2>System <span>Statistics</span></h2><p>Explore the opportunities and resources available through Youth Education Support.</p></div>
<div class="dashboard-grid">
<a class="dashboard-stat" href="scholarships.php"><span class="dashboard-icon">🎓</span><h2><?= $scholarships ?></h2><p>Scholarships</p></a>
<a class="dashboard-stat" href="materials.php"><span class="dashboard-icon">📚</span><h2><?= $materials ?></h2><p>Study Materials</p></a>
<a class="dashboard-stat" href="events.php"><span class="dashboard-icon">📅</span><h2><?= $events ?></h2><p>Events</p></a>
<a class="dashboard-stat" href="tutors.php"><span class="dashboard-icon">👨‍🏫</span><h2><?= $tutors ?></h2><p>Tutors</p></a>
</div></section>
<section class="section dashboard-section">
<div class="section-heading"><span class="section-kicker">QUICK ACCESS</span><h2>What would you like to <span>explore?</span></h2><p>Choose a service below to continue.</p></div>
<div class="service-grid dashboard-services">
<a class="service-card service-scholarship" href="scholarships.php"><div class="service-icon">🎓</div><span class="card-tag">FUND YOUR STUDIES</span><h3>Scholarships</h3><p>View available funding opportunities and scholarship information.</p><span class="card-link">Explore scholarships →</span></a>
<a class="service-card service-materials" href="materials.php"><div class="service-icon">📚</div><span class="card-tag">KEEP LEARNING</span><h3>Study Materials</h3><p>Access learning resources, notes and educational materials.</p><span class="card-link">View resources →</span></a>
<a class="service-card service-events" href="announcements.php"><div class="service-icon">📢</div><span class="card-tag">STAY INFORMED</span><h3>Announcements</h3><p>Stay updated with the latest news and important announcements.</p><span class="card-link">View announcements →</span></a>
<a class="service-card service-events" href="events.php"><div class="service-icon">📅</div><span class="card-tag">GET INVOLVED</span><h3>Events</h3><p>Discover upcoming educational events, activities and workshops.</p><span class="card-link">View events →</span></a>
<a class="service-card service-tutoring" href="tutors.php"><div class="service-icon">👨‍🏫</div><span class="card-tag">ACADEMIC SUPPORT</span><h3>Tutors</h3><p>Find tutoring support and improve your academic confidence.</p><span class="card-link">Find tutors →</span></a>
<a class="service-card service-volunteer" href="volunteer.php"><div class="service-icon">🤝</div><span class="card-tag">MAKE A DIFFERENCE</span><h3>Volunteer</h3><p>Share your time and skills to help other students.</p><span class="card-link">Get involved →</span></a>
<a class="service-card service-donate" href="donations.php"><div class="service-icon">💰</div><span class="card-tag">SUPPORT EDUCATION</span><h3>Donations</h3><p>Support educational initiatives and help students access opportunities.</p><span class="card-link">Support students →</span></a>
<a class="service-card service-tutoring" href="profile.php"><div class="service-icon">👤</div><span class="card-tag">YOUR ACCOUNT</span><h3>Profile</h3><p>View and manage your personal account information.</p><span class="card-link">Manage profile →</span></a>
</div></section>
</main>
<footer class="site-footer"><div class="footer-bottom"><span>© <?= date('Y') ?> Youth Education Support. All rights reserved.</span><span>Learning today. Building tomorrow.</span></div></footer>
<script src="darkmode.js"></script><script>function toggleSiteMenu(){const n=document.getElementById('mainNav');const b=document.querySelector('.menu-toggle');const o=n.classList.toggle('is-open');b.setAttribute('aria-expanded',o?'true':'false');}</script>
</body></html>
