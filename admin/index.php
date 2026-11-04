<?php
declare(strict_types=1);
require '../require_login.php';

$conn = require '../inc/db.php';
require '../inc/functions.php';

$result = mysqli_execute_query(
    $conn,
    'SELECT email FROM users WHERE id = ?',
    [$_SESSION['user_id']]
);
$user = mysqli_fetch_assoc($result);
?>
<h1>Cookbook admin</h1>
<p>Signed in as <?= e($user['email']) ?></p>
<a href="../logout.php">Logout</a>