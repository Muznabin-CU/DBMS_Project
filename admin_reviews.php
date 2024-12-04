<?php
session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'myanimeverse1');

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reviews
$reviews_result = $conn->query("SELECT reviews.id, reviews.rating, reviews.review, users.username, anime.name AS anime_name 
                                FROM reviews 
                                JOIN users ON reviews.user_id = users.id 
                                JOIN anime ON reviews.anime_id = anime.id");

// Handle removing reviews
if (isset($_GET['delete_review_id'])) {
    $review_id = $_GET['delete_review_id'];
    $conn->query("DELETE FROM reviews WHERE id=$review_id");
    header('Location: reviews.php'); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - AnimeVerse</title>
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
        <h1>Manage Reviews</h1>
        <ul>
            <?php while ($row = $reviews_result->fetch_assoc()) { ?>
                <li>
                    <strong><?php echo $row['anime_name']; ?></strong> by <em><?php echo $row['username']; ?></em>
                    <p>Rating: <?php echo $row['rating']; ?></p>
                    <p><?php echo $row['review']; ?></p>
                    <a href="?delete_review_id=<?php echo $row['id']; ?>">Delete Review</a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
