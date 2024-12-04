<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'myanimeverse1');

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all reviews
$reviews = $conn->query("SELECT r.id, r.anime_id, r.user_id, r.rating, r.review, a.name AS anime_name, u.username AS user_name 
                         FROM reviews r 
                         JOIN anime a ON r.anime_id = a.id 
                         JOIN users u ON r.user_id = u.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - AnimeVerse</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div class="logo">AnimeVerse</div>
    <ul>
        <li><a href="user_dashboard.php">Dashboard</a></li>
        <li><a href="anime_list.php">Anime List</a></li>
        <li><a href="reviews.php">Reviews</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="container">
    <h1>All Reviews</h1>

    <ul>
        <?php while ($review = $reviews->fetch_assoc()) { ?>
            <li>
                <strong><?php echo $review['anime_name']; ?></strong> - Rating: <?php echo $review['rating']; ?><br>
                <em>Reviewed by <?php echo $review['user_name']; ?></em><br>
                <p><?php echo $review['review']; ?></p>
            </li>
        <?php } ?>
    </ul>

</div>

<footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
