<?php
declare(strict_types=1);

function get_value(string $key): string {
    return trim($_GET[$key] ?? '');
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$query = get_value('q');
$results = [];
$error = '';

$recipes = [
    'Pasta Carbonara',
    'Chicken Tikka Masala',
    'Fish Tacos',
    'Tacos al Pastor',
    'Vegetable Stir Fry'
];

if ($query !== '') {
    foreach($recipes as $recipe) {
        if (str_contains(strtolower($recipe), strtolower($query))) {
            $results[] = $recipe;
        }
    }
} else {
    $error = 'Please enter a search query.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
        .error {
            color: red;
        }
    </style>
    <h1>Search For a Recipe</h1>
    <form action="index.php" method="get">
        <label for="q">Recipe name </label>
        <input type="search" id="q" name="q" value="">
        <button type="submit">Search</button>
    </form>
    <?php if ($error !== '') : ?>
        <p class="error"><?= e($error); ?></p>
    <?php elseif ($results !== []) : ?>
        <p> There are <?= count($results); ?> result(s) for "<?= e($query); ?>":</p>
        <ul> <?php foreach ($results as $recipe) : ?>
            <li><?= e($recipe); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>