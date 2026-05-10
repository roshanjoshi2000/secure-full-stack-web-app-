<?php
session_start();
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Generate CAPTCHA if not already set
if (!isset($_SESSION['captcha_question']) || !isset($_SESSION['captcha_answer'])) {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_question'] = "$num1 + $num2";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

$username = '';
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');

    if ($username === '') {
        $errors[] = "Username is required.";
    }

    if ($password === '') {
        $errors[] = "Password is required.";
    }

    if ($confirm_password === '') {
        $errors[] = "Please confirm your password.";
    }

    if ($password !== '' && $confirm_password !== '' && $password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if ($captcha === '') {
        $errors[] = "CAPTCHA answer is required.";
    } elseif ((int)$captcha !== (int)$_SESSION['captcha_answer']) {
        $errors[] = "CAPTCHA answer is incorrect.";
    }

    if (empty($errors)) {
        $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        if (!$check_stmt) {
            $errors[] = "Prepare failed: " . $mysqli->error;
        } else {
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $errors[] = "Username already exists.";
            }

            $check_stmt->close();
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $insert_stmt = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        if (!$insert_stmt) {
            $errors[] = "Prepare failed: " . $mysqli->error;
        } else {
            $insert_stmt->bind_param("ss", $username, $password_hash);

            if ($insert_stmt->execute()) {
                $message = "Registration successful. You can now log in.";
                $username = '';
            } else {
                $errors[] = "Registration failed: " . $insert_stmt->error;
            }

            $insert_stmt->close();
        }
    }

    // Regenerate CAPTCHA after every submit
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_question'] = "$num1 + $num2";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

echo $twig->render('register.twig', [
    'title' => 'Register',
    'logged_in_user' => $_SESSION['username'] ?? null,
    'username' => $username,
    'errors' => $errors,
    'message' => $message,
    'captcha_question' => $_SESSION['captcha_question']
]);