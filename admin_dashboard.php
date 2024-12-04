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

// Handle adding anime
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_anime'])) {
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $thumbnail = $_POST['thumbnail'];
    $description = $_POST['description'];

    // Prepare the SQL query to insert anime into the database
    $stmt = $conn->prepare("INSERT INTO anime (name, genre, thumbnail, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $genre, $thumbnail, $description);
    $stmt->execute();
    $stmt->close();
}

// Handle removing anime
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM anime WHERE id=$id");
}

// Fetch anime list
$anime_result = $conn->query("SELECT * FROM anime");

// Fetch reviews
$reviews_result = $conn->query("SELECT reviews.id, reviews.rating, reviews.review, users.username, anime.name AS anime_name 
                                FROM reviews 
                                JOIN users ON reviews.user_id = users.id 
                                JOIN anime ON reviews.anime_id = anime.id");

// Handle removing reviews
if (isset($_GET['delete_review_id'])) {
    $review_id = $_GET['delete_review_id'];
    $conn->query("DELETE FROM reviews WHERE id=$review_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AnimeVerse</title>
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
        <h1>Admin Dashboard</h1>
        <h2>Add Anime</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Anime Name" required>
            <input type="text" name="genre" placeholder="Genre" required>
            <input type="text" name="thumbnail" placeholder="Thumbnail URL" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <button type="submit" name="add_anime">Add Anime</button>
        </form>

        <h2>Anime List</h2>
        <ul>
            <?php while ($row = $anime_result->fetch_assoc()) { ?>
                <li>
                    <img src="<?php echo $row['thumbnail']; ?>" alt="Anime Thumbnail" width="50" height="50">
                    <strong><?php echo $row['name']; ?></strong> (<?php echo $row['genre']; ?>)
                    <p><?php echo $row['description']; ?></p>
                    <a href="?delete_id=<?php echo $row['id']; ?>">Delete</a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
