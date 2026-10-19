
<?php

session_start();
require_once "db.php";

// Only administrators may manage users.
if (
    !isset($_SESSION['user'], $_SESSION['role']) ||
    $_SESSION['role'] !== "admin"
) {
    header("Location: login.php");
    exit();
}

// Escape values before displaying them in HTML.
function escapeUserValue($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$search = trim((string)($_GET['search'] ?? ''));
$result = false;
$queryError = false;

// Search using a prepared statement to prevent SQL injection.
if ($search !== '') {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, fullname, email, role, created_at
         FROM users
         WHERE fullname LIKE ? OR email LIKE ?
         ORDER BY id ASC"
    );

    if ($stmt) {
        $searchTerm = "%" . $search . "%";

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $searchTerm,
            $searchTerm
        );

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
        } else {
            error_log("User search failed: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    } else {
        error_log("User search preparation failed: " . mysqli_error($conn));
    }
} else {
    // Display all users when no search term is entered.
    $result = mysqli_query(
        $conn,
        "SELECT id, fullname, email, role, created_at
         FROM users
         ORDER BY id ASC"
    );
}

if ($result === false) {
    $queryError = true;
    error_log("Manage users query failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users - Youth Education Support</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .users-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 10px;
            border: 1px solid #444;
            text-align: left;
        }

        .users-table th {
            background: #0077cc;
            color: #ffffff;
        }

        .users-table tbody tr:nth-child(even) {
            background: #f1f1f1;
        }

        .users-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .users-notice {
            padding: 12px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .users-error {
            background: #f8d7da;
            color: #721c24;
        }

        .users-empty {
            background: #eaf4fc;
            color: #234;
        }

        .users-search {
            max-width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 600px) {
            .users-table th,
            .users-table td {
                padding: 8px;
            }
        }
    </style>

</head>

<body>

<header>

    <h1>Users</h1>

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

<main class="container">

    <section class="card">

        <h2>Manage Users</h2>

        <form method="GET" action="manage_users.php">

            <input
                class="users-search"
                type="text"
                name="search"
                placeholder="Search users..."
                value="<?= escapeUserValue($search) ?>"
                maxlength="100"
            >

            <button type="submit">Search</button>

            <?php if ($search !== ''): ?>
                <a href="manage_users.php">Clear Search</a>
            <?php endif; ?>

        </form>

    </section>

    <section class="card">

        <h2>User Accounts</h2>

        <?php if ($queryError): ?>

            <p class="users-notice users-error" role="alert">
                Users could not be loaded. Please try again later.
            </p>

        <?php elseif (mysqli_num_rows($result) === 0): ?>

            <p class="users-notice users-empty">
                No users found.
            </p>

        <?php else: ?>

            <div class="users-table-wrapper">

                <table class="users-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    // These are display numbers, not database IDs.
                    $displayNumber = 1;

                    while ($row = mysqli_fetch_assoc($result)):
                        $actualId = (int)$row['id'];
                    ?>

                        <tr>

                            <td><?= $displayNumber++ ?></td>

                            <td>
                                <?= escapeUserValue($row['fullname']) ?>
                            </td>

                            <td>
                                <?= escapeUserValue($row['email']) ?>
                            </td>

                            <td>
                                <?= escapeUserValue($row['role']) ?>
                            </td>

                            <td>
                                <?= escapeUserValue($row['created_at']) ?>
                            </td>

                            <td>
                                <a href="edit_user.php?id=<?= $actualId ?>">
                                    Edit
                                </a>

                                |

                                <a
                                    href="delete_user.php?id=<?= $actualId ?>"
                                    onclick="return confirm('Delete this user?')"
                                >
                                    Delete
                                </a>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

<script src="darkmode.js"></script>

<?php
if (!$queryError && $result !== false) {
    mysqli_free_result($result);
}
?>

</body>
</html>
