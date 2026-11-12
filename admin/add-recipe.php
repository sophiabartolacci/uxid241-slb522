<?php
declare(strict_types=1);

require '../require_login.php';

$conn = require '../inc/db.php';
require '../inc/functions.php';

$categories = get_categories($conn);

$name = '';
$subtitle = '';
$category_id = '';
$servings = '';
$description = '';
$error = '';
$ingredient_names = [''];
$ingredient_quantities = [''];
$ingredient_units = [''];
$step_titles = [''];
$step_instructions_list = [''];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = post_value('name');
    $subtitle = post_value('subtitle');
    $category_id = post_value('category_id');
    $servings = post_value('servings');
    $description = post_value('description');
    $csrf_token = post_value('csrf_token');
    $ingredient_names = $_POST['ingredient_name'] ?? [''];
    $ingredient_quantities = $_POST['ingredient_quantity'] ?? [''];
    $ingredient_units = $_POST['ingredient_unit'] ?? [''];
    $step_titles = $_POST['step_title'] ?? [''];
    $step_instructions_list = $_POST['step_instructions'] ?? [''];

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    if (isset($_POST['add_ingredient'])) {
        $ingredient_names[] = '';
        $ingredient_quantities[] = '';
        $ingredient_units[] = '';
    } elseif (isset($_POST['add_step'])) {
        $step_titles[] = '';
        $step_instructions_list[] = '';
    } elseif (isset($_POST['save_recipe'])) {
        if ($name === '') {
            $error = 'Recipe name is required';
        } elseif ($category_id === '') {
            $error = 'Category is required';
        } elseif (
            $servings === '' || !ctype_digit($servings) || (int) $servings < 1
        ) {
            $error = 'Servings must be a positive number';
        } else {
            mysqli_execute_query(
                $conn,
                'INSERT INTO recipes
                    (name, subtitle, category_id, servings, description)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $name,
                    $subtitle !== '' ? $subtitle : null,
                    (int) $category_id,
                    (int) $servings,
                    $description !== '' ? $description : null
                ]
            );

            $recipe_id = (int) mysqli_insert_id($conn);
            $sort_order = 1;

            foreach ($ingredient_names as $index => $ingredient_name) {
                $ingredient_name = trim((string) $ingredient_name);

                if ($ingredient_name === '') {
                    continue;
                }
                $ingredient_quantity = trim((string) ($ingredient_quantities[$index] ?? ''));
                $ingredient_unit = trim((string) ($ingredient_units[$index] ?? ''));
                
                mysqli_execute_query(
                    $conn,
                    'INSERT INTO ingredients
                        (recipe_id, sort_order, quantity, unit, name)
                     VALUES (?, ?, ?, ?, ?)',
                    [
                        $recipe_id,
                        $sort_order,
                        $ingredient_quantity !== '' ? $ingredient_quantity : null,
                        $ingredient_unit !== '' ? $ingredient_unit : null,
                        $ingredient_name
                    ]
                );
                $sort_order++;
            }
            $step_number = 1;

            foreach ($step_instructions_list as $index => $step_instructions) {
                $step_instructions = trim((string) $step_instructions);

                if ($step_instructions === '') {
                    continue;
                }
                $step_title = trim((string) ($step_titles[$index] ?? ''));

                mysqli_execute_query(
                    $conn,
                    'INSERT INTO steps
                        (recipe_id, step_number, step_title, instructions)
                     VALUES (?, ?, ?, ?)',
                    [
                        $recipe_id,
                        $step_number,
                        $step_title !== '' ? $step_title : null,
                        $step_instructions
                    ]
                );
                $step_number++;
            }
            $_SESSION['success'] = 'Recipe added successfully.';
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <main class="admin-form-page">
        <a class="back-link" href="index.php">Back to Admin</a>

        <h1>Add Recipe</h1>

        <?php if ($error !== '') : ?>
            <p class="error"><?= e($error); ?></p>
        <?php endif; ?>

        <form class="recipe-form"method="post">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']); ?>"
            >

            <label for="name">Recipe Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= e($name); ?>"
                required
            >

            <label for="subtitle">Subtitle</label>
            <input
                type="text"
                id="subtitle"
                name="subtitle"
                value="<?= e($subtitle); ?>"
            >

            <label for="category_id">Category</label>
            <select
                id="category_id"
                name="category_id"
                required
            >
                <option value="">Select a Category</option>

                <?php foreach ($categories as $category) : ?>
                    <option
                        value="<?= e((string) $category['id']); ?>"
                        <?= $category_id === (string) $category['id']
                            ? 'selected'
                            : ''; ?>
                    >
                        <?= e($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="servings">Servings</label>
            <input
                type="number"
                id="servings"
                name="servings"
                min="1"
                value="<?= e($servings); ?>"
                required
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
            ><?= e($description); ?></textarea>

            <h2 id="ingredients">Ingredients</h2>
            <?php foreach ($ingredient_names as $index => $ingredient_name) : ?>
                <!--
                AI: ChatGPT assisted with keeping the page positioned at the newly
                added ingredient after the PHP form reloads.
                -->
                <fieldset 
                    class="ingredient-fieldset"
                    <?= $index === array_key_last($ingredient_names) ? 'id="ingredients-new"' : ''; ?>
                >
                    <legend>Ingredient <?= $index + 1; ?></legend>
                    <div class="form-field">
                        <label for="ingredient_quantity_<?= $index; ?>">
                            Quantity
                        </label>
                        <input
                            type="text"
                            id="ingredient_quantity_<?= $index; ?>"
                            name="ingredient_quantity[]"
                            placeholder="e.g. 1 1/2"
                            value="<?= e((string) ($ingredient_quantities[$index] ?? '')); ?>"
                        >
                    </div>
                    <div class="form-field">
                        <label for="ingredient_unit_<?= $index; ?>">
                            Unit
                        </label>
                        <input
                            type="text"
                            id="ingredient_unit_<?= $index; ?>"
                            name="ingredient_unit[]"
                            placeholder="e.g. cups"
                            value="<?= e((string) ($ingredient_units[$index] ?? '')); ?>"
                        >
                    </div>
                    <div class="form-field">
                        <label for="ingredient_name_<?= $index; ?>">
                            Ingredient
                        </label>
                        <input
                            type="text"
                            id="ingredient_name_<?= $index; ?>"
                            name="ingredient_name[]"
                            placeholder="e.g. flour"
                            value="<?= e((string) $ingredient_name); ?>"
                        >
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <button 
                type="submit" 
                name="add_ingredient" 
                formaction="add-recipe.php#ingredients-new" 
                formnovalidate
            >
                Add Ingredient
            </button>

            <h2 id="steps">Steps</h2>

            <?php foreach ($step_instructions_list as $index => $step_instructions) : ?>
                <!--
                AI: ChatGPT assisted with keeping the page positioned at the newly
                added steps after the PHP form reloads.
                -->
                <fieldset 
                    class="step-fieldset"
                    <?= $index === array_key_last($step_instructions_list) ? 'id="steps-new"' : ''; ?>
                >
                    <legend>Step <?= $index + 1; ?></legend>
                    <div class="form-field">
                        <label for="step_title_<?= $index; ?>">
                            Title
                        </label>
                        <input
                            type="text"
                            id="step_title_<?= $index; ?>"
                            name="step_title[]"
                            placeholder="e.g. Prepare the ingredients"
                            value="<?= e((string) ($step_titles[$index] ?? '')); ?>"
                        >
                    </div>
                    <div class="form-field">
                        <label for="step_instructions_<?= $index; ?>">
                            Instructions
                        </label>
                        <textarea
                            id="step_instructions_<?= $index; ?>"
                            name="step_instructions[]"
                            placeholder="Describe what to do in this step."
                        ><?= e((string) $step_instructions); ?></textarea>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <button 
                type="submit" 
                name="add_step" 
                formaction="add-recipe.php#steps-new"
                formnovalidate
            >
                Add Step
            </button>

            <button type="submit" name="save_recipe">
                Add Recipe
            </button>
        </form>
    </main>
</body>
</html>