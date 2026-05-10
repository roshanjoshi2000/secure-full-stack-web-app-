<?php
session_start();
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$movie_name = $_GET['movie_name'] ?? '';
$genre = $_GET['genre'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$year = $_GET['year'] ?? '';

$conditions = [];
$params = [];
$types = "";

if ($movie_name !== '') {
    $conditions[] = "Movie_name LIKE ?";
    $params[] = "%" . $movie_name . "%";
    $types .= "s";
}

if ($genre !== '') {
    $conditions[] = "Genre LIKE ?";
    $params[] = "%" . $genre . "%";
    $types .= "s";
}

if ($min_price !== '' && is_numeric($min_price)) {
    $conditions[] = "Price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price !== '' && is_numeric($max_price)) {
    $conditions[] = "Price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

if ($year !== '' && is_numeric($year)) {
    $conditions[] = "YEAR(Date_of_release) = ?";
    $params[] = (int)$year;
    $types .= "i";
}

$sql = "SELECT * FROM movies";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY Date_of_release";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo $twig->render('search.twig', [
    'title' => 'Search Movies',
    'logged_in_user' => $_SESSION['username'] ?? null,
    'movie_name' => $movie_name,
    'genre' => $genre,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'year' => $year,
    'movies' => $movies
]);