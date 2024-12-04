<?php
session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'myanimeverse1');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle removing a user
if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];
    $conn->query("DELETE FROM users WHERE id=$user_id");
}

// Fetch all users except admins
$users_result = $conn->query("SELECT * FROM users WHERE role='user'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users - AnimeVerse</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar">
        <div class="logo">AnimeVerse</div>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_reviews.php">Reviews</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Admin Users</h1>
        <h2>Manage Registered Users</h2>
        <ul>
            <?php while ($row = $users_result->fetch_assoc()) { ?>
                <li>
                    <strong><?php echo $row['username']; ?></strong> (<?php echo $row['email']; ?>)
                    <a href="?delete_user_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
