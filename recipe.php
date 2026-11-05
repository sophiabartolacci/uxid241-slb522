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
    <link rel="stylesheet" href="css/recipe.css">
</head>
<body>
    <a class="admin-login-button" href="login.php">Admin Login</a>
    <a class="back-link" href="index.php">Back to recipes</a>

    <?php if ($error !== '') : ?>
        <p class="error"><?= e($error); ?></p>
    <?php else : ?>
        <main class="recipe-details">
            <header class="recipe-header">
                <h1><?= e($recipe['name']); ?></h1>

                <?php if (!empty($recipe['subtitle'])) : ?>
                    <p class="recipe-subtitle"><?= e($recipe['subtitle']); ?></p>
                <?php endif; ?>
            </header>

            <?php if (!empty($recipe['main_image'])) : ?>
                <img 
                    class="recipe-main-image"
                    src="<?= e($recipe['main_image']); ?>"
                    alt="<?= e($recipe['name']); ?>"
                    >
            <?php endif; ?> 
            <div class="recipe-info">
                <span>Serves <?= e((string) $recipe['servings']); ?></span>

                <?php if (!empty($recipe['category'])) : ?>
                    <span>•</span>
                    <span><?= e($recipe['category']); ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($recipe['description'])) : ?>
                <p class="recipe-description"><?= e($recipe['description']); ?></p>
            <?php endif; ?>
            
            <section class="ingredients-section">
                <h2>Ingredients</h2>
                <div class="ingredients-layout">
                    <?php if (!empty($recipe['ingredients_image'])) : ?>
                        <img
                            class="ingredients-image"
                            src="<?= e($recipe['ingredients_image']); ?>"
                            alt="Ingredients for <?= e($recipe['name']); ?>"
                        >
                    <?php endif; ?>

                    <?php if (count($ingredients) > 0) : ?>
                        <ul class="ingredient-list">
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
                </div>
            </section>

            <section class="steps-section">
                <h2>Steps</h2>
                <?php if (count($steps) > 0) : ?>
                    <div class="steps-list">
                        <?php foreach ($steps as $step) : ?>
                            <article class="recipe-step">
                                <div class="step-header">
                                    <p class="step-number">
                                        <?= e((string) $step['step_number']); ?>
                                    </p>
                                    <?php if (!empty($step['step_title'])) : ?>
                                        <h3><?= e($step['step_title']); ?></h3>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="step-content">
                                    <?php if (!empty($step['image'])) : ?>
                                        <img
                                            class="step-image"
                                            src="<?= e($step['image']); ?>"
                                            alt="Step <?= e((string) $step['step_number']); ?> for <?= e($recipe['name']); ?>"
                                        >
                                    <?php endif; ?>
                                    <p><?= e($step['instructions']); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No steps available.</p>
                <?php endif; ?>
            </section>

        </main>
    <?php endif; ?>
</body>
</html>