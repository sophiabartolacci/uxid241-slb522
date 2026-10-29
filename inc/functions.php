<?php
declare(strict_types=1);

function get_value(string $key): string {
    return trim($_GET[$key] ?? '');
}

function post_value(string $key): string {
    return trim($_POST[$key] ?? '');
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function get_all_recipes(mysqli $conn): array {
    $sql = 'SELECT recipes.*, categories.name AS category
            FROM recipes 
            LEFT JOIN categories ON recipes.category_id = categories.id
            ORDER BY recipes.id ASC;';
    return mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
}

function get_recipe(mysqli $conn, int $id): array {
    $sql = 'SELECT id, name
            FROM recipes 
            WHERE id = ?;';
    return mysqli_fetch_assoc(mysqli_execute_query($conn, $sql, [$id]));
}

function search_recipes(mysqli $conn, string $query, string $category_id): array {
  $sql = 'SELECT recipes.*, categories.name AS category
          FROM recipes
          LEFT JOIN categories ON recipes.category_id = categories.id';
  $params = [];

  if ($query !== '' && $category_id !== '') {
    $sql .= ' WHERE recipes.name LIKE ?
             AND recipes.category_id = ?';
    $params[] = '%' . $query . '%';
    $params[] = (int) $category_id;
  } elseif ($query !== '') {
    $sql .= ' WHERE recipes.name LIKE ?';
    $params[] = '%' . $query . '%';
  } else if ($category_id !== '') {
    $sql .= ' WHERE recipes.category_id = ?';
    $params[] = (int) $category_id;
  }
  $sql .= ' ORDER BY recipes.id ASC;';
  return mysqli_fetch_all(mysqli_execute_query($conn, $sql, $params), MYSQLI_ASSOC);
}

function get_all_categories(mysqli $conn): array {
    $sql = 'SELECT id, name
            FROM categories 
            ORDER BY id ASC;';
    return mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
}