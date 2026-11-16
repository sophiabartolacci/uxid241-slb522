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

function get_recipe(mysqli $conn, int $id): ?array {
    $sql = 'SELECT recipes.*, categories.name AS category
            FROM recipes 
            LEFT JOIN categories ON recipes.category_id = categories.id
            WHERE recipes.id = ?;';
    return mysqli_fetch_assoc(mysqli_execute_query($conn, $sql, [$id]));
}

function get_ingredients(mysqli $conn, int $id): array {
  $sql = 'SELECT *
          FROM ingredients
          WHERE recipe_id = ?
          ORDER BY sort_order ASC;';
  return mysqli_fetch_all(mysqli_execute_query($conn, $sql, [$id]), MYSQLI_ASSOC);
}

function get_steps(mysqli $conn, int $id): array {
  $sql = 'SELECT *
          FROM steps
          WHERE recipe_id = ?
          ORDER BY step_number ASC;';
  return mysqli_fetch_all(mysqli_execute_query($conn, $sql, [$id]), MYSQLI_ASSOC);
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
    $sql = 'SELECT DISTINCT categories.id, categories.name
            FROM categories
            INNER JOIN recipes ON categories.id = recipes.category_id 
            ORDER BY categories.id ASC;';
    return mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
}

function get_categories(mysqli $conn): array {
    $sql = 'SELECT id, name
            FROM categories
            ORDER BY id ASC;';
    return mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
}


function make_safe_name(
    string $original_name,
    string $ext,
    string $folder
): string {
    $base = pathinfo($original_name, PATHINFO_FILENAME);
    $base = strtolower($base);
    $base = preg_replace('/[^a-z0-9]+/', '-', $base) ?? '';
    $base = trim($base, '-');
    $base = substr($base, 0, 60);

    if ($base === '') {
        $base = 'upload';
    }

    $safe_name = $base . '.' . $ext;
    $counter = 1;
    while (file_exists($folder . '/' . $safe_name)) {
        $safe_name = $base . '-' . $counter . '.' . $ext;
        $counter++;
    }
    return $safe_name;
}

function save_upload(
    string $tmp_name,
    string $original_name,
    int $size,
    int $error,
    string $folder
): string {
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed');
    }

    $max_bytes = 2 * 1024 * 1024;
    if ($size > $max_bytes) {
        throw new RuntimeException('File too large');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp_name);

    $ext_map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($ext_map[$mime])) {
        throw new RuntimeException('Type not allowed');
    }

    $ext = $ext_map[$mime];
    $safe_name = make_safe_name($original_name, $ext, $folder);

    if (!move_uploaded_file($tmp_name, $folder . '/' . $safe_name)) {
        throw new RuntimeException('Could not save file');
    }

    return $safe_name;
}
