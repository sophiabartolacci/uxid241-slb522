<?php
declare(strict_types=1);

$conn = require 'inc/db.php';
require 'inc/functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = post_value('email');
    $plain = $_POST['password'] ?? '';
    // $hash = password_hash($plain, PASSWORD_DEFAULT);

    $result = mysqli_execute_query(
        $conn,
        'SELECT id, password_hash FROM users WHERE email = ?',
        [$email]
    );
    $user = mysqli_fetch_assoc($result);

    if ($user !== null && password_verify($plain, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        header('Location: admin/index.php');
        exit;
    }
    $error = 'Invalid email or password.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($error)): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="jane@example.com" required>
        <input type="password" name="password" placeholder="password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>