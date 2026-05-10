<?php
session_start();
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Generate CAPTCHA if not exists
if (!isset($_SESSION['captcha_question']) || !isset($_SESSION['captcha_answer'])) {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_question'] = "$num1 + $num2";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

$username = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');

    if ($username === '') {
        $errors[] = "Username is required.";
    }

    if ($password === '') {
        $errors[] = "Password is required.";
    }

    if ($captcha === '') {
        $errors[] = "CAPTCHA answer is required.";
    } elseif ((int)$captcha !== (int)$_SESSION['captcha_answer']) {
        $errors[] = "CAPTCHA answer is incorrect.";
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        if (!$stmt) {
            $errors[] = "Prepare failed: " . $mysqli->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    header("Location: list_of_movie.php");
                    exit();
                } else {
                    $errors[] = "Invalid username or password.";
                }
            } else {
                $errors[] = "Invalid username or password.";
            }

            $stmt->close();
        }
    }

    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_question'] = "$num1 + $num2";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

echo $twig->render('login.twig', [
    'title' => 'Login',
    'username' => $username,
    'errors' => $errors,
    'captcha_question' => $_SESSION['captcha_question']
]);