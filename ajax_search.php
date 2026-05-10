<?php
require_once 'db_connect.php';

$movie_name = trim($_GET['movie_name'] ?? '');

if ($movie_name === '') {
    exit;
}

$stmt = $mysqli->prepare("
    SELECT id, Movie_name, Genre, Price, Date_of_release
    FROM movies
    WHERE Movie_name LIKE ?
    ORDER BY Movie_name ASC
");

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$search_term = "%" . $movie_name . "%";
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table border="1" cellpadding="8" cellspacing="0">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Movie Name</th>';
    echo '<th>Genre</th>';
    echo '<th>Price</th>';
    echo '<th>Date of Release</th>';
    echo '</tr>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Movie_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Genre']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Price']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Date_of_release']) . '</td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo '<p>No movies found.</p>';
}

$stmt->close();
$mysqli->close();
?>