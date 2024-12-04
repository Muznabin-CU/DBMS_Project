<?php
session_start();

// Check if the user is logged in as 'user'
if ($_SESSION['role'] !== 'user') {
    header('Location: login.php'); // Redirect to login page if not a user
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'myanimeverse1');

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user ID is set and valid
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo "<p>Error: User ID is not set or invalid.</p>";
    exit();
}

// Handle search
$search_result = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_result = $conn->query("SELECT * FROM anime WHERE name LIKE '%$search%'");
}

// Handle adding review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anime_id'], $_POST['rating'], $_POST['review'])) {
    $anime_id = $_POST['anime_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $stmt = $conn->prepare("INSERT INTO reviews (anime_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $anime_id, $user_id, $rating, $review);
    if ($stmt->execute()) {
        echo "<p>Review added successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch user's reviews
$user_reviews = $conn->query("SELECT reviews.id, reviews.rating, reviews.review, anime.name AS anime_name 
                              FROM reviews 
                              JOIN anime ON reviews.anime_id = anime.id 
                              WHERE reviews.user_id = $user_id");

// Handle deleting reviews
if (isset($_GET['delete_review_id'])) {
    $review_id = $_GET['delete_review_id'];
    $conn->query("DELETE FROM reviews WHERE id = $review_id");
    header("Location: user_dashboard.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - AnimeVerse</title>
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
        <h1>User Dashboard</h1>
        
        <!-- Search Anime -->
        <form method="GET">
            <input type="text" name="search" placeholder="Search Anime by Name" required>
            <button type="submit">Search</button>
        </form>

        <?php if ($search_result && $search_result->num_rows > 0) { ?>
            <h2>Search Results</h2>
            <ul>
                <?php while ($anime = $search_result->fetch_assoc()) { ?>
                    <li>
                        <img src="<?php echo $anime['thumbnail']; ?>" alt="Anime Thumbnail" width="50" height="50">
                        <strong><?php echo $anime['name']; ?></strong> (<?php echo $anime['genre']; ?>)
                        <p><?php echo $anime['description']; ?></p>

                        <!-- Review Form -->
                        <form method="POST">
                            <input type="hidden" name="anime_id" value="<?php echo $anime['id']; ?>">
                            <input type="number" name="rating" min="1" max="10" placeholder="Rating (1-10)" required>
                            <textarea name="review" placeholder="Write a review" required></textarea>
                            <button type="submit">Submit Review</button>
                        </form>
                    </li>
                <?php } ?>
            </ul>
        <?php } elseif (isset($search)) { ?>
            <p>No anime found with the name "<?php echo htmlspecialchars($search); ?>"</p>
        <?php } ?>

        <!-- User Reviews -->
        <h2>Your Reviews</h2>
        <ul>
            <?php while ($review = $user_reviews->fetch_assoc()) { ?>
                <li>
                    <strong><?php echo $review['anime_name']; ?></strong>: <?php echo $review['review']; ?> (Rating: <?php echo $review['rating']; ?>/10)
                    <a href="?delete_review_id=<?php echo $review['id']; ?>">Delete</a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
