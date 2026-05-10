<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie ID.");
}

$id = (int) $_GET['id'];

$stmt = $mysqli->prepare("SELECT Movie_name, Genre, Price, Date_of_release FROM movies WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Movie not found.");
}

$movie = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_stmt = $mysqli->prepare("DELETE FROM movies WHERE id = ?");
    if (!$delete_stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $delete_stmt->close();
        $mysqli->close();
        header("Location: list_of_movie.php");
        exit();
    } else {
        die("Delete failed: " . $delete_stmt->error);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Delete Movie</title>
</head>
<body>
    <h1>Delete Movie</h1>

    <p>
        <a href="list_of_movie.php">Cancel and go back</a> |
        <a href="logout.php">Logout</a>
    </p>

    <p>Are you sure you want to delete this movie?</p>

    <ul>
        <li><strong>Movie Name:</strong> <?= htmlspecialchars($movie['Movie_name']) ?></li>
        <li><strong>Genre:</strong> <?= htmlspecialchars($movie['Genre']) ?></li>
        <li><strong>Price:</strong> <?= htmlspecialchars($movie['Price']) ?></li>
        <li><strong>Date of Release:</strong> <?= htmlspecialchars($movie['Date_of_release']) ?></li>
    </ul>

    <form method="post" action="">
        <button type="submit">Yes, Delete</button>
    </form>
</body>
</html>