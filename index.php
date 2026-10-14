<?php
declare(strict_types=1);
require 'inc/db.php';

function get_value(string $key): string {
    return trim($_GET[$key] ?? '');
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$query = get_value('q');
$results = [];
$error = '';

$db_result = mysqli_query($conn, 'SELECT * FROM recipes');
$recipes = mysqli_fetch_all($db_result, MYSQLI_ASSOC);

if (isset($_GET['q'])) {
    if ($query !== '') {
        foreach($recipes as $recipe) {
            if (str_contains(strtolower($recipe['title']), strtolower($query))) {
                $results[] = $recipe;
            }
        }
    } else {
        $error = 'Please enter a search query.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookbook</title>

    <style>
        .error {
            color: red;
        }
        .table-container {
            overflow-x: auto;   
        }

        table {
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #eee;
        }

        td {
            min-width: 100px;
        }

        td:nth-child(7) {
            min-width: 350px;
        }
    </style>
</head>
<body>
    <h1>Search For a Recipe</h1>
    <form action="index.php" method="get">
        <label for="q">Recipe name </label>
        <input type="search" id="q" name="q" value="">
        <button type="submit">Search</button>
    </form>
    <?php if ($error !== '') : ?>
        <p class="error"><?= e($error); ?></p>
    <?php elseif ($query !== '') : ?>
        <p> There are <?= count($results); ?> result(s) for "<?= e($query); ?>":</p>
        <ul> <?php foreach ($results as $recipe) : ?>
            <li><?= e($recipe['title']); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <div class="table-container">
        <h2>All Recipes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Slug</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Category</th>
                    <th>Servings</th>
                    <th>Description</th>
                    <th>Ingredients</th>
                    <th>Steps</th>
                    <th>Main Image</th>
                    <th>Ingredients Image</th>
                    <th>Image Folder</th>
                    <th>Source PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recipes as $recipe) : ?>
                <tr>
                    <td><?= e((string) $recipe['id']); ?></td>
                    <td><?= e($recipe['slug']); ?></td>
                    <td><?= e($recipe['title']); ?></td>
                    <td><?= e($recipe['subtitle'] ?? ''); ?></td>
                    <td><?= e($recipe['category'] ?? ''); ?></td>
                    <td><?= e((string) ($recipe['servings'] ?? '')); ?></td>
                    <td><?= e($recipe['description'] ?? ''); ?></td>
                    <td><?= e((string) ($recipe['ingredient_count'] ?? '')); ?></td>
                    <td><?= e((string) ($recipe['step_count'] ?? '')); ?></td>
                    <td><?= e($recipe['main_image'] ?? ''); ?></td>
                    <td><?= e($recipe['ingredients_image'] ?? ''); ?></td>
                    <td><?= e($recipe['image_folder'] ?? ''); ?></td>
                    <td><?= e($recipe['source_pdf'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>