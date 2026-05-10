<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$movie_name = '';
$genre = '';
$price = '';
$date_of_release = '';
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_name = trim($_POST['movie_name'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $price = $_POST['price'] ?? '';
    $date_of_release = $_POST['date_of_release'] ?? '';

    if ($movie_name === '') $errors[] = "Movie name required";
    if ($genre === '') $errors[] = "Genre required";
    if (!is_numeric($price)) $errors[] = "Invalid price";
    if ($date_of_release === '') $errors[] = "Date required";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO movies (Movie_name, Genre, Price, Date_of_release) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $movie_name, $genre, $price, $date_of_release);

        if ($stmt->execute()) {
            $message = "Movie added successfully";
            $movie_name = $genre = $price = $date_of_release = '';
        }
    }
}

echo $twig->render('add.twig', [
    'title' => 'Add Movie',
    'logged_in_user' => $_SESSION['username'],
    'movie_name' => $movie_name,
    'genre' => $genre,
    'price' => $price,
    'date_of_release' => $date_of_release,
    'message' => $message,
    'errors' => $errors
]);