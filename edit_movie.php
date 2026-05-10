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

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_name = $_POST['movie_name'];
    $genre = $_POST['genre'];
    $price = $_POST['price'];
    $date = $_POST['date_of_release'];

    $stmt = $mysqli->prepare("UPDATE movies SET Movie_name=?, Genre=?, Price=?, Date_of_release=? WHERE id=?");
    $stmt->bind_param("ssdsi", $movie_name, $genre, $price, $date, $id);
    $stmt->execute();

    $message = "Updated successfully";
}

echo $twig->render('edit.twig', [
    'title' => 'Edit Movie',
    'logged_in_user' => $_SESSION['username'],
    'movie_name' => $movie['Movie_name'],
    'genre' => $movie['Genre'],
    'price' => $movie['Price'],
    'date' => $movie['Date_of_release'],
    'message' => $message,
    'errors' => $errors
]);