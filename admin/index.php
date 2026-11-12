<?php
declare(strict_types=1);
require '../require_login.php';

$conn = require '../inc/db.php';
require '../inc/functions.php';

$result = mysqli_execute_query(
    $conn,
    'SELECT email FROM users WHERE id = ?',
    [$_SESSION['user_id']]
);
$user = mysqli_fetch_assoc($result);
$recipes = get_all_recipes($conn);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <main class="admin-page">
        <header class="admin-header">
            <nav class="admin-nav">
                <a href="../index.php">View Recipe Library</a>
                <a href="../logout.php">Logout</a>
            </nav>
            <h1>Admin</h1>
            <p class="admin-user">Signed in as <?= e($user['email']); ?></p>
        </header>

        <?php if ($success !== '') : ?>
            <p class="success"><?= e($success); ?></p>
        <?php endif; ?>

        <section class="admin-recipes">
            <div class="admin-section-header">
                <h2>Manage Recipes</h2>
                <a class="primary-link" href="add-recipe.php">
                    Add Recipe
                </a>
            </div>

            <?php if (count($recipes) > 0) : ?>
                <ul class="admin-recipe-list">
                    <?php foreach ($recipes as $recipe) : ?>
                        <li class="admin-recipe-item">
                            <span class="admin-recipe-name">
                                <?= e($recipe['name']); ?>
                            </span>

                            <div class="admin-recipe-actions">
                                <a
                                    class="edit-link"
                                    href="edit-recipe.php?id=<?= e((string) $recipe['id']); ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    class="delete-form"
                                    action="delete-recipe.php"
                                    method="post"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= e((string) $recipe['id']); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($_SESSION['csrf_token']); ?>"
                                    >

                                    <button
                                        class="delete-button"
                                        type="submit"
                                        onclick="return confirm('Are you sure you want to delete this recipe?');"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No recipes available.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>