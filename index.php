<?php
declare(strict_types=1);
require 'inc/db.php';
require 'inc/functions.php';

$query = get_value('q');
$category_id = get_value('category');
$error = '';

$categories = get_all_categories($conn);

if ($query !== '' || $category_id !== '') {
    $recipes = search_recipes($conn, $query, $category_id);
} else {
    $recipes = get_all_recipes($conn);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookbook</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Search For a Recipe</h1>
    <form action="index.php" method="get">
        <label for="q">Recipe name </label>
        <input type="search" id="q" name="q" value="<?= e($query); ?>">
        <label for="category">Category</label>
        <select name="category" id="category">
            <option value="">All categories</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= e((string) $category['id']); ?>"
                    <?= $category_id === (string) $category['id'] ? 'selected' : ''; ?>>
                    <?= e($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <?php if ($error !== '') : ?>
        <p class="error"><?= e($error); ?></p>
    <?php elseif ($query !== '' || $category_id !== '') : ?>
        <p> There are <?= count($recipes); ?> result(s) for "<?= e($query); ?>":
        </p>
    <?php endif; ?>
    
    <div class="table-container">
        <?php if ($query !== '' || $category_id !== '') : ?>
            <h2>Recipes</h2>
        <?php else : ?>
            <h2>All Recipes</h2>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Slug</th>
                    <th>Name</th>
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
                    <td>
                        <a href="recipe.php?id=<?= e((string) $recipe['id']); ?>">
                            <?= e($recipe['name']); ?>
                        </a>
                    </td>
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