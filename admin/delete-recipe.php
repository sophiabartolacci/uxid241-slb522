<?php
declare(strict_types=1);
require '../require_login.php';

$conn = require '../inc/db.php';
require '../inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) post_value('id');
$csrf_token = post_value('csrf_token');

if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

if ($id < 1) {
    http_response_code(400);
    exit('Invalid recipe');
}

mysqli_execute_query(
    $conn,
    'DELETE FROM recipes WHERE id = ?',
    [$id]
);
$_SESSION['success'] = 'Recipe deleted successfully.';
header('Location: index.php');
exit;

?>