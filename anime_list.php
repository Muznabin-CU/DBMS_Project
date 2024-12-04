<?php
session_start();

// Check if the user is logged in as 'user' or 'admin'
if ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'myanimeverse1');

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all anime from the database
$anime_list = $conn->query("SELECT * FROM anime");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime List - AnimeVerse</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Main anime item styling */
        .anime-item {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .anime-details {
            margin-left: 10px;
        }

        /* Hidden popup styling */
        .details-popup {
            display: none;
            position: absolute;
            top: 50%;
            left: 110%;
            width: 250px;
            padding: 15px;
            border: 1px solid #007bff;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10;
            border-radius: 8px;
        }

        .details-popup h4 {
            margin: 0 0 10px 0;
        }

        /* Show popup on hover */
        .anime-item:hover .details-popup {
            display: block;
        }

        /* Prevent overlap */
        .anime-list {
            position: relative;
        }
    </style>
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
        <h1>Anime List</h1>

        <ul class="anime-list">
            <?php while ($anime = $anime_list->fetch_assoc()) { ?>
                <li>
                    <div class="anime-item">
                        <img src="<?php echo $anime['thumbnail']; ?>" alt="Anime Thumbnail" width="100" height="100">
                        <div class="anime-details">
                            <strong><?php echo $anime['name']; ?></strong>
                            <p>Genre: <?php echo $anime['genre']; ?></p>
                            <a href="#" class="view-details">View Details</a>
                        </div>

                        <!-- Hidden Popup for Description -->
                        <div class="details-popup">
                            <h4><?php echo $anime['name']; ?></h4>
                            <p><strong>Genre:</strong> <?php echo $anime['genre']; ?></p>
                            <p><strong>Description:</strong> <?php echo $anime['description']; ?></p>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

    <footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
