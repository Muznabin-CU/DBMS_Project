<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the selected role from the form

    try {
        if ($role === 'admin') {
            // Admin login
            if ($username === 'admin1' && $password === '22701069') {
                $_SESSION['user_id'] = 1; // Admin ID (can be replaced with a database ID if needed)
                $_SESSION['role'] = 'admin';
                header('Location: admin_dashboard.php');
                exit;
            } else {
                echo "<p style='color: red;'>Invalid admin credentials.</p>";
            }
        } else {
            // User login
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header('Location: user_dashboard.php');
                exit;
            } else {
                echo "<p style='color: red;'>Invalid username or password.</p>";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>

<div class="navbar">
    <div class="logo">AnimeVerse</div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="signup.php">Sign Up</a></li>
    </ul>
</div>

<div class="form-container">
    <h1>Login</h1>
    <form method="POST">
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
</div>

<footer>&copy; 2024 AnimeVerse. All Rights Reserved.</footer>

</body>
</html>
