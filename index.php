<?php
declare(strict_types=1);
require 'inc/db.php';
require 'inc/functions.php';

$query = get_value('q');
$category_id = get_value('category');

$categories = get_all_categories($conn);
$category_name = '';

foreach ($categories as $category) {
    if ((string) $category['id'] === $category_id) {
        $category_name = $category['name'];
        break;
    }
}

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
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <a class="admin-login-button" href="login.php">Admin Login</a>

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

    <?php if ($query !== '' && $category_id !== '') : ?>
        <p>There are <?= count($recipes); ?> result(s) for "<?= e($query); ?>" in <?= e($category_name); ?>:</p>
    <?php elseif ($query !== '') : ?>
        <p>There are <?= count($recipes); ?> result(s) for "<?= e($query); ?>":</p>
    <?php elseif ($category_id !== '') : ?>
        <p>There are <?= count($recipes); ?> result(s) in <?= e($category_name); ?>:</p>
    <?php endif; ?>

    <section class="recipes-section">
    <?php if ($query !== '' || $category_id !== '') : ?>
        <h2>Recipes</h2>
    <?php else : ?>
        <h2>All Recipes</h2>
    <?php endif; ?>

    <?php if (count($recipes) > 0) : ?>
        <div class="recipe-grid">
            <?php foreach ($recipes as $recipe) : ?>
                <article class="recipe-card">
                    <a href="recipe.php?id=<?= e((string) $recipe['id']); ?>">

                        <?php if (!empty($recipe['main_image'])) : ?>
                            <img
                                class="recipe-card-image"
                                src="<?= e($recipe['main_image']); ?>"
                                alt="<?= e($recipe['name']); ?>"
                            >
                        <?php endif; ?>

                        <div class="recipe-card-content">
                            <h3><?= e($recipe['name']); ?></h3>

                            <?php if (!empty($recipe['subtitle'])) : ?>
                                <p class="recipe-card-subtitle">
                                    <?= e($recipe['subtitle']); ?>
                                </p>
                            <?php endif; ?>

                            <div class="recipe-card-info">
                                <?php if (!empty($recipe['category'])) : ?>
                                    <span><?= e($recipe['category']); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($recipe['servings'])) : ?>
                                    <span>Serves <?= e((string) $recipe['servings']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                    </a>
                </article>
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <p>No recipes found. Try a different search or category.</p>
    <?php endif; ?>
</section>
</body>
</html>