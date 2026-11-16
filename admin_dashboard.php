<?php
require_once __DIR__ . "/includes/auth.php";
auth_require_role('admin');
require_once __DIR__ . "/db.php";
function yesAdminCount($conn, $table) {
    $allowed = ['users','scholarships','announcements','events','tutors','contacts','tutor_bookings','scholarship_applications','volunteers','donations'];
    if (!in_array($table, $allowed, true)) return 0;
    $q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$table`");
    if (!$q) return 0;
    $r = mysqli_fetch_assoc($q);
    return (int)($r['total'] ?? 0);
}
$users=yesAdminCount($conn,'users'); $scholarships=yesAdminCount($conn,'scholarships');
$announcements=yesAdminCount($conn,'announcements'); $events=yesAdminCount($conn,'events');
$tutors=yesAdminCount($conn,'tutors'); $messages=yesAdminCount($conn,'contacts');
$bookings=yesAdminCount($conn,'tutor_bookings'); $applications=yesAdminCount($conn,'scholarship_applications');
$volunteers=yesAdminCount($conn,'volunteers'); $donations=yesAdminCount($conn,'donations');
$fullname=htmlspecialchars($_SESSION['fullname'],ENT_QUOTES,'UTF-8');
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Youth Education Support</title>
<link rel="icon" type="image/svg+xml" href="assets/yes-logo.svg"><link rel="stylesheet" href="style.css?v=8">
</head><body class="dashboard-layout has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>


<main>
<section class="dashboard-welcome admin-welcome"><div class="dashboard-welcome-inner"><div><span class="section-kicker">ADMINISTRATION PANEL</span><h1>Welcome back, <?= $fullname ?>!</h1><p>Manage the Youth Education Support platform, monitor activities and keep educational services organised.</p></div><img src="https://www.invemar.org.co/documents/37438/100039/DSC_1031.JPG.jpg/82c7f7ec-2c7d-3c57-de0b-5f7f8c2daf1c?t=1697813160853" alt="Illustration of an administrator managing education services"></div></section>
<section class="section dashboard-section"><div class="section-heading"><span class="section-kicker">PLATFORM OVERVIEW</span><h2>System <span>Statistics</span></h2><p>View an overview of the records and activities managed through the platform.</p></div>
<div class="dashboard-grid">
<a class="dashboard-stat" href="manage_users.php"><span class="dashboard-icon">👥</span><h2><?= $users ?></h2><p>Users</p></a>
<a class="dashboard-stat" href="manage_scholarships.php"><span class="dashboard-icon">🎓</span><h2><?= $scholarships ?></h2><p>Scholarships</p></a>
<a class="dashboard-stat" href="manage_announcements.php"><span class="dashboard-icon">📢</span><h2><?= $announcements ?></h2><p>Announcements</p></a>
<a class="dashboard-stat" href="manage_events.php"><span class="dashboard-icon">📅</span><h2><?= $events ?></h2><p>Events</p></a>
<a class="dashboard-stat" href="manage_tutors.php"><span class="dashboard-icon">👨‍🏫</span><h2><?= $tutors ?></h2><p>Tutors</p></a>
<a class="dashboard-stat" href="manage_bookings.php"><span class="dashboard-icon">📘</span><h2><?= $bookings ?></h2><p>Tutor Bookings</p></a>
<a class="dashboard-stat" href="manage_applications.php"><span class="dashboard-icon">📝</span><h2><?= $applications ?></h2><p>Applications</p></a>
<a class="dashboard-stat" href="manage_volunteers.php"><span class="dashboard-icon">🤝</span><h2><?= $volunteers ?></h2><p>Volunteers</p></a>
<a class="dashboard-stat" href="manage_donations.php"><span class="dashboard-icon">💰</span><h2><?= $donations ?></h2><p>Donations</p></a>
<a class="dashboard-stat" href="manage_contacts.php"><span class="dashboard-icon">✉</span><h2><?= $messages ?></h2><p>Messages</p></a>
</div></section>
<section class="section dashboard-section"><div class="section-heading"><span class="section-kicker">MANAGEMENT TOOLS</span><h2>Quick <span>Actions</span></h2><p>Manage the platform's main services and records.</p></div>
<div class="service-grid dashboard-services">
<a class="service-card service-scholarship" href="manage_scholarships.php"><div class="service-icon">🎓</div><span class="card-tag">FUNDING</span><h3>Scholarships</h3><p>Manage scholarship opportunities and information.</p><span class="card-link">Manage scholarships →</span></a>
<a class="service-card service-events" href="manage_announcements.php"><div class="service-icon">📢</div><span class="card-tag">COMMUNICATION</span><h3>Announcements</h3><p>Publish and manage important announcements.</p><span class="card-link">Manage announcements →</span></a>
<a class="service-card service-events" href="manage_events.php"><div class="service-icon">📅</div><span class="card-tag">ACTIVITIES</span><h3>Events</h3><p>Create and update educational events.</p><span class="card-link">Manage events →</span></a>
<a class="service-card service-tutoring" href="manage_tutors.php"><div class="service-icon">👨‍🏫</div><span class="card-tag">ACADEMIC SUPPORT</span><h3>Tutors</h3><p>Manage tutor information and availability.</p><span class="card-link">Manage tutors →</span></a>
<a class="service-card service-materials" href="manage_bookings.php"><div class="service-icon">📘</div><span class="card-tag">TUTORING</span><h3>Bookings</h3><p>Review and manage tutoring bookings.</p><span class="card-link">Manage bookings →</span></a>
<a class="service-card service-scholarship" href="manage_applications.php"><div class="service-icon">📝</div><span class="card-tag">APPLICATIONS</span><h3>Scholarship Applications</h3><p>Review student scholarship applications.</p><span class="card-link">Manage applications →</span></a>
<a class="service-card service-volunteer" href="manage_volunteers.php"><div class="service-icon">🤝</div><span class="card-tag">COMMUNITY</span><h3>Volunteers</h3><p>View and manage volunteer information.</p><span class="card-link">Manage volunteers →</span></a>
<a class="service-card service-donate" href="manage_donations.php"><div class="service-icon">💰</div><span class="card-tag">SUPPORT</span><h3>Donations</h3><p>Review donation records.</p><span class="card-link">Manage donations →</span></a>
</div>
<div class="admin-extra-actions"><a href="manage_users.php">Manage Users →</a><a href="manage_materials.php">Manage Study Materials →</a><a href="manage_contacts.php">Manage Contact Messages →</a><a href="create_admin_invite.php">Generate Admin Invitation →</a></div>
</section></main>
<footer class="site-footer"><div class="footer-bottom"><span>© <?= date('Y') ?> Youth Education Support. All rights reserved.</span><span>Administrator Panel</span></div></footer>
<script src="darkmode.js"></script><script>function toggleSiteMenu(){const n=document.getElementById('mainNav');const b=document.querySelector('.menu-toggle');const o=n.classList.toggle('is-open');b.setAttribute('aria-expanded',o?'true':'false');}</script>
</body></html>
