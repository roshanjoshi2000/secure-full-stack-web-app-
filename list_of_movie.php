<?php
session_start();
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$sql = "SELECT * FROM movies ORDER BY Date_of_release";
$result = $mysqli->query($sql);

$movies = [];

while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo $twig->render('list.twig', [
    'title' => 'Movie List',
    'movies' => $movies,
    'logged_in_user' => $_SESSION['username'] ?? null
]);