<?php
require_once __DIR__ . '/auth.php';
if (!auth_is_logged_in()) { return; }
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$current = basename($_SERVER['PHP_SELF'] ?? '');
$studentLinks = [
 ['index.php','Home','⌂'],['dashboard.php','Dashboard','▦'],
 ['scholarships.php','Scholarships','🎓'],['materials.php','Resources','▤'],
 ['tutors.php','Tutoring','♧'],['events.php','Events','▣'],
 ['announcements.php','Announcements','▤'],['volunteer.php','Volunteer','♡'],
 ['contact.php','Contact','✉'],['profile.php','Profile','●']
];
$adminLinks = [
 ['index.php','Home','⌂'],['admin_dashboard.php','Dashboard','▦'],
 ['manage_scholarships.php','Scholarships','🎓'],['manage_materials.php','Resources','▤'],
 ['manage_tutors.php','Tutors','♧'],['manage_bookings.php','Tutor Bookings','▣'],
 ['manage_events.php','Events','▣'],['manage_announcements.php','Announcements','▤'],
 ['manage_applications.php','Applications','▤'],['manage_users.php','Users','●'],
 ['manage_volunteers.php','Volunteers','♡'],
 ['manage_contacts.php','Contact Messages','✉'],['create_admin_invite.php','Admin Invitations','＋'],
 ['profile.php','Profile','●']
];
$links = $isAdmin ? $adminLinks : $studentLinks;
?>
<aside class="app-sidebar" id="appSidebar">
 <a class="app-sidebar-brand" href="index.php">
  <img src="assets/yes-logo.svg" alt="Youth Education Support">
  <span>Youth Education<br><strong>Support</strong></span>
 </a>
 <div class="app-sidebar-role"><?= $isAdmin ? 'ADMINISTRATION' : 'STUDENT PORTAL' ?></div>
 <nav class="app-sidebar-nav" aria-label="Main navigation">
 <?php foreach ($links as $link): ?>
  <a href="<?= htmlspecialchars($link[0], ENT_QUOTES, 'UTF-8') ?>" class="<?= $current === $link[0] ? 'active' : '' ?>">
   <span class="sidebar-link-icon"><?= htmlspecialchars($link[2], ENT_QUOTES, 'UTF-8') ?></span>
   <span><?= htmlspecialchars($link[1], ENT_QUOTES, 'UTF-8') ?></span>
  </a>
 <?php endforeach; ?>
 <a class="sidebar-donate" href="<?= $isAdmin ? 'manage_donations.php' : 'donations.php' ?>">
  <span class="sidebar-link-icon">♥</span><span><?= $isAdmin ? 'Manage Donations' : 'Donate' ?></span>
 </a>
 </nav>
 <div class="app-sidebar-bottom">
  <a href="logout.php?switch=1"><span class="sidebar-link-icon">⇄</span>Switch Account</a>
  <a href="logout.php"><span class="sidebar-link-icon">↪</span>Logout</a>
 </div>
</aside>
<button class="sidebar-mobile-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" onclick="toggleAppSidebar()">☰</button>
<script>
function toggleAppSidebar(){
 const side=document.getElementById('appSidebar');
 const btn=document.querySelector('.sidebar-mobile-toggle');
 const opened=side.classList.toggle('sidebar-open');
 btn.setAttribute('aria-expanded',opened?'true':'false');
}
</script>
