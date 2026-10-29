<?php
declare(strict_types=1);
require 'inc/db.php';
require 'inc/functions.php';

$id = (int) get_value('id');
$recipe = get_recipe($conn, $id);

if ($recipe === null) {
    http_response_code(404);
    $error = 'Recipe not found.';
} else {
    $error = '';
    $ingredients = get_ingredients($conn, $id);
    $steps = get_steps($conn, $id);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $recipe !== null ? e($recipe['name']) : 'Recipe'; ?> | Cookbook</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a href="index.php">Back to recipes</a>
    <?php if ($error !== '') : ?>
        <p class="error"><?= e($error); ?></p>
    <?php else : ?>
        <p><?= e($recipe['category'] ?? ''); ?></p>
        <h1><?= e($recipe['name']); ?></h1>
        <?php if (!empty($recipe['subtitle'])) : ?>
            <h2><?= e($recipe['subtitle']); ?></h2>
        <?php endif; ?>
        <p>Serves <?= e((string) $recipe['servings']); ?></p>
        <?php if (!empty($recipe['description'])) : ?>
            <p><?= e($recipe['description']); ?></p>
        <?php endif; ?>
        <h2>Ingredients</h2>
        <?php if (count($ingredients) > 0) : ?>
            <ul>
                <?php foreach ($ingredients as $ingredient) : ?>
                    <li>
                        <?= e($ingredient['quantity'] ?? ''); ?>
                        <?= e($ingredient['unit'] ?? ''); ?>
                        <?= e($ingredient['name'] ?? ''); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No ingredients available.</p>
        <?php endif; ?>        
        <h2>Steps</h2>
        <?php if (count($steps) > 0) : ?>
            <ol>
                <?php foreach ($steps as $step) : ?>
                    <li>
                        <?php if (!empty($step['step_title'])) : ?>
                            <h3><?= e($step['step_title']); ?></h3>
                        <?php endif; ?>
                        <p><?= e($step['instructions']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p>No steps available.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>