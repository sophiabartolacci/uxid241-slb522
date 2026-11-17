<?php
declare(strict_types=1);

require '../require_login.php';

$conn = require '../inc/db.php';
require '../inc/functions.php';

$id = (int) get_value('id');
$recipe = get_recipe($conn, $id);
$categories = get_categories($conn);
$error = '';
$upload_folder = __DIR__ . '/../images/uploads';

if ($recipe === null) {
    http_response_code(404);
    exit('Recipe not found');
}

$ingredients = get_ingredients($conn, $id);
$steps = get_steps($conn, $id);

$name = $recipe['name'];
$subtitle = $recipe['subtitle'] ?? '';
$category_id = (string) ($recipe['category_id'] ?? '');
$servings = (string) ($recipe['servings'] ?? '');
$description = $recipe['description'] ?? '';

$ingredient_names = [];
$ingredient_quantities = [];
$ingredient_units = [];

foreach ($ingredients as $ingredient) {
    $ingredient_names[] = $ingredient['name'];
    $ingredient_quantities[] = $ingredient['quantity'] ?? '';
    $ingredient_units[] = $ingredient['unit'] ?? '';
}

if (count($ingredient_names) === 0) {
    $ingredient_names[] = '';
    $ingredient_quantities[] = '';
    $ingredient_units[] = '';
}

$step_titles = [];
$step_instructions_list = [];
$step_images = [];

foreach ($steps as $step) {
    $step_titles[] = $step['step_title'] ?? '';
    $step_instructions_list[] = $step['instructions'];
    $step_images[] = $step['image'] ?? '';
}

if (count($step_instructions_list) === 0) {
    $step_titles[] = '';
    $step_instructions_list[] = '';
    $step_images[] = '';
}

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
    $step_images = $_POST['existing_step_image'] ?? [''];

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    if (isset($_POST['save_recipe'])) {
        $main_image = $recipe['main_image'] ?? null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $saved_image = save_upload(
                    $_FILES['main_image']['tmp_name'],
                    $_FILES['main_image']['name'],
                    $_FILES['main_image']['size'],
                    $_FILES['main_image']['error'],
                    $upload_folder
                );
                $main_image = 'images/uploads/' . $saved_image;
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            }
        }
        $ingredients_image = $recipe['ingredients_image'] ?? null;
        if (isset($_FILES['ingredients_image']) && $_FILES['ingredients_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $saved_image = save_upload(
                    $_FILES['ingredients_image']['tmp_name'],
                    $_FILES['ingredients_image']['name'],
                    $_FILES['ingredients_image']['size'],
                    $_FILES['ingredients_image']['error'],
                    $upload_folder
                );
                $ingredients_image = 'images/uploads/' . $saved_image;
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            }
        }
        if ($name === '') {
            $error = 'Recipe name is required';
        } elseif ($category_id === '') {
            $error = 'Category is required';
        } elseif (
            $servings === '' ||
            !ctype_digit($servings) ||
            (int) $servings < 1
        ) {
            $error = 'Servings must be a positive number';
        } elseif ($error === '') {
            mysqli_execute_query(
                $conn,
                'UPDATE recipes
                 SET name = ?,
                     subtitle = ?,
                     category_id = ?,
                     servings = ?,
                     description = ?,
                     main_image = ?,
                     ingredients_image = ?
                 WHERE id = ?',
                [
                    $name,
                    $subtitle !== '' ? $subtitle : null,
                    (int) $category_id,
                    (int) $servings,
                    $description !== '' ? $description : null,
                    $main_image,
                    $ingredients_image,
                    $id
                ]
            );

            mysqli_execute_query(
                $conn,
                'DELETE FROM ingredients WHERE recipe_id = ?',
                [$id]
            );

            $sort_order = 1;

            foreach ($ingredient_names as $index => $ingredient_name) {
                $ingredient_name = trim((string) $ingredient_name);

                if ($ingredient_name === '') {
                    continue;
                }

                $ingredient_quantity = trim(
                    (string) ($ingredient_quantities[$index] ?? '')
                );

                $ingredient_unit = trim(
                    (string) ($ingredient_units[$index] ?? '')
                );

                mysqli_execute_query(
                    $conn,
                    'INSERT INTO ingredients
                        (recipe_id, sort_order, quantity, unit, name)
                     VALUES (?, ?, ?, ?, ?)',
                    [
                        $id,
                        $sort_order,
                        $ingredient_quantity !== ''
                            ? $ingredient_quantity
                            : null,
                        $ingredient_unit !== ''
                            ? $ingredient_unit
                            : null,
                        $ingredient_name
                    ]
                );

                $sort_order++;
            }

            foreach ($step_instructions_list as $index => $step_instructions) {
                $step_image = $step_images[$index] ?? null;

                if (
                    isset($_FILES['step_image']['error'][$index]) &&
                    $_FILES['step_image']['error'][$index] !== UPLOAD_ERR_NO_FILE
                ) {
                    try {
                        $saved_image = save_upload(
                            $_FILES['step_image']['tmp_name'][$index],
                            $_FILES['step_image']['name'][$index],
                            $_FILES['step_image']['size'][$index],
                            $_FILES['step_image']['error'][$index],
                            $upload_folder
                        );

                        $step_image = 'images/uploads/' . $saved_image;
                    } catch (RuntimeException $e) {
                        $error = $e->getMessage();
                        break;
                    }
                }

                $step_images[$index] = $step_image;
            }

            if ($error === '') {
                mysqli_execute_query(
                    $conn,
                    'DELETE FROM steps WHERE recipe_id = ?',
                    [$id]
                );

                $step_number = 1;

                foreach ($step_instructions_list as $index => $step_instructions) {
                    $step_instructions = trim((string) $step_instructions);

                    if ($step_instructions === '') {
                        continue;
                    }

                    $step_title = trim(
                        (string) ($step_titles[$index] ?? '')
                    );
                    $step_image = $step_images[$index] ?? null;

                    mysqli_execute_query(
                        $conn,
                        'INSERT INTO steps
                            (recipe_id, step_number, step_title, instructions, image)
                        VALUES (?, ?, ?, ?, ?)',
                        [
                            $id,
                            $step_number,
                            $step_title !== '' ? $step_title : null,
                            $step_instructions,
                            $step_image
                        ]
                    );

                    $step_number++;
                }
                if ($error === '') {
                    $_SESSION['success'] = 'Recipe updated successfully.';
                    header('Location: index.php');
                    exit;
                }
            }
        }     
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <main class="admin-form-page">
        <a class="back-link" href="index.php">Back to Admin</a>

        <h1>Edit Recipe</h1>

        <?php if ($error !== '') : ?>
            <p class="error"><?= e($error); ?></p>
        <?php endif; ?>

        <form class="recipe-form" method="post" enctype="multipart/form-data">
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

            <h2>Recipe Image</h2>
            <?php if (!empty($recipe['main_image'])) : ?>
                <div class="current-image">
                    <p>Current Image</p>
                    <img src="../<?= e($recipe['main_image']); ?>" 
                     alt="<?= e($recipe['name']); ?>">
                </div>
            <?php endif; ?>

            <div class="form-field">
                <label for="main_image">
                    <?= !empty($recipe['main_image']) ? 'Replace Image' : 'Upload Image'; ?>
                </label>
                <input 
                    type="file"
                    id="main_image"
                    name="main_image"
                    accept=".jpeg, .jpg, .png, .webp"
                    >
            </div>

            <h2 id="ingredients">Ingredients</h2>
            <?php if (!empty($recipe['ingredients_image'])) : ?>
                <div class="current-image">
                    <p>Current Image</p>
                    <img 
                        src="../<?= e($recipe['ingredients_image']); ?>" 
                        alt="Ingredients for <?= e($recipe['name']); ?>"
                    >
                </div>
            <?php endif; ?>
            <div class="form-field">
                <label for="ingredients_image">
                    <?= !empty($recipe['ingredients_image']) ? 'Replace Image' : 'Upload Image'; ?>
                </label>
                <input 
                    type="file"
                    id="ingredients_image"
                    name="ingredients_image"
                    accept=".jpeg, .jpg, .png, .webp"
                >
            </div>

            <div id="ingredient-fields">
                <?php foreach ($ingredient_names as $index => $ingredient_name) : ?>
                    <fieldset class="ingredient-fieldset">
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
            </div>

            <button 
                type="button" 
                id="add-ingredient" 
            >
                Add Ingredient
            </button>

            <h2 id="steps">Steps</h2>
            <div id="step-fields">
                <?php foreach ($step_instructions_list as $index => $step_instructions) : ?>
                    <fieldset class="step-fieldset">
                        <legend>Step <?= $index + 1; ?></legend>
                        <input 
                            type="hidden"
                            name="existing_step_image[]"
                            value="<?= e((string) ($step_images[$index] ?? '')); ?>"
                        >
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
                        <?php if (!empty($step_images[$index])) : ?>
                            <div class="current-image">
                                <p>Current Image</p>
                                <img 
                                    src="../<?= e($step_images[$index]); ?>" 
                                    alt="Step <?= $index + 1; ?>"
                                >
                            </div>
                        <?php endif; ?>
                        <div class="form-field">
                            <label for="step_image_<?= $index; ?>">
                                <?= !empty($step_images[$index]) ? 'Replace Image' : 'Upload Image'; ?>
                            </label>
                            <input 
                                type="file"
                                id="step_image_<?= $index; ?>"
                                name="step_image[]"
                                accept=".jpeg, .jpg, .png, .webp"
                            >
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </div>

            <button 
                type="button" 
                id="add-step" 
            >
                Add Step
            </button>

            <button type="submit" name="save_recipe">
                Save Changes
            </button>
        </form>
    </main>
    <script src="../src/recipe-form.js"></script>
</body>
</html>