<?php

require_once __DIR__ . "/includes/auth.php";
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$notice = '';
$error = '';
if (isset($_POST['add'])) {
    $title = trim($_POST['title'] ?? '');
    $provider = trim($_POST['provider'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $application_url = trim($_POST['application_url'] ?? '');
    if ($application_url !== '' && (!filter_var($application_url, FILTER_VALIDATE_URL) ||
        !in_array(strtolower((string)parse_url($application_url, PHP_URL_SCHEME)), ['http','https'], true))) {
        $error = 'Please enter a valid website URL beginning with https://';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO scholarships (title, provider, deadline, description, application_url) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $title, $provider, $deadline, $description, $application_url);
            if (mysqli_stmt_execute($stmt)) $notice = 'Scholarship added successfully.';
            else $error = 'Unable to add scholarship. Please check the database update has been applied.';
            mysqli_stmt_close($stmt);
        } else $error = 'Unable to save scholarship. Apply the supplied database update first.';
    }
}

$result=mysqli_query($conn,
"SELECT * FROM scholarships");

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Scholarships</title>

<link rel="stylesheet" href="style.css?v=8">

</head>

<body class="has-app-sidebar">
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>


<header>

<h1>Scholarships</h1>

<nav>

<a href="admin_dashboard.php">Dashboard</a>
<a href="manage_users.php">Users</a>
<a href="manage_scholarships.php">Scholarships</a>
<a href="manage_materials.php">Materials</a>
<a href="manage_announcements.php">Announcements</a>
<a href="manage_events.php">Events</a>
<a href="manage_tutors.php">Tutors</a>
<a href="manage_bookings.php">Bookings</a>
<a href="manage_applications.php">Applications</a>
<a href="manage_volunteers.php">Volunteers</a>
<a href="manage_donations.php">Donations</a>
<a href="manage_contacts.php">Messages</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>

</nav>

</header>
<?php $pageHeroTitle = 'Manage Scholarships'; $pageHeroText = 'Create and maintain scholarship opportunities.'; $pageHeroImage = 'https://images.pexels.com/photos/7713349/pexels-photo-7713349.jpeg?auto=compress&cs=tinysrgb&w=1400'; require __DIR__ . "/includes/page_hero.php"; ?>


<div class="container">

<form method="POST">

<h2>Add Scholarship</h2>
<?php if ($notice !== ''): ?><div class="success"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<input
type="text"
name="title"
placeholder="Scholarship Title"
required>

<input
type="text"
name="provider"
placeholder="Provider"
required>

<input
type="date"
name="deadline"
required>

<textarea
name="description"
placeholder="Description"
required
style="width:100%;height:120px;"></textarea>

<label for="application_url">Official application website link (optional)</label>
<input type="url" id="application_url" name="application_url" placeholder="https://www.example.org/apply">
<small>Students will be redirected to this link when they click Apply.</small>

<button
type="submit"
name="add">

Add Scholarship

</button>

</form>

<br>

<div class="card">

<h2>All Scholarships</h2>

<table border="1" width="100%" cellpadding="10">

<tr>

<th>ID</th>
<th>Title</th>
<th>Provider</th>
<th>Deadline</th>
<th>Application Link</th>
<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['provider']; ?></td>

<td><?= htmlspecialchars($row['deadline'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?php if (!empty($row['application_url'])): ?><a href="<?= htmlspecialchars($row['application_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">Open link ↗</a><?php else: ?>Not set<?php endif; ?></td>
<td>

<a href="delete_scholarship.php?id=<?php echo $row['id']; ?>">

Delete

</a>

|

<a href="edit_scholarship.php?id=<?php echo $row['id']; ?>">

Edit

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script src="darkmode.js"></script>

</body>
</html>