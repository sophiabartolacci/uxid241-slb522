<?php
declare(strict_types=1);
$conn = require 'inc/db.php';
require 'inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = post_value('email');
    $plain = $_POST['password'] ?? '';
    $hash = password_hash($plain, PASSWORD_DEFAULT);

    mysqli_execute_query(
        $conn,
        'INSERT INTO users (email, password_hash) VALUES (?, ?)',
        [$email, $hash]
    );
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Create an Account</h1>
    <form method="post">
        <input type="email" name="email" placeholder="jane@example.com" required>
        <input type="password" name="password" placeholder="password" required>
        <button type="submit">Submit</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>