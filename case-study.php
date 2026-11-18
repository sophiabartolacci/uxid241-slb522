<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookbook Case Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/case-study.css">
</head>
<body>
    <main class="case-study">
        <header class="case-study-hero">
            <h1>Cookbook Case Study</h1>
            <p>Designing and developing a searchable recipe library that keeps
                information easy to reference while cooking.</p>
            <img
                src="images/case-study/homepage.png"
                alt="Cookbook homepage showing recipe search, category filtering, and recipe cards"
            >
        </header>
        <section class="case-study-section">
            <h2>Overview</h2>

            <p>The Cookbook is a searchable recipe library designed around actually using a recipe while cooking. It organizes ingredients, quantities, instructions, and images into clear sections so users can quickly find what they need and, after looking away to cook, easily find their place again.</p>
            <p>Behind the public recipe library is an authenticated admin area for maintaining recipes without directly editing the database. The project uses PHP, MySQL, and JavaScript to support browsing, search and filtering, recipe management, and image uploads while keeping the interface simple and focused on the content.</p>
        </section>
        <section class="case-study-section">
            <h2>Context and Challenge</h2>
            <p>The project was developed independently over one term for two types of users. Visitors needed a simple way to find recipes and follow them while actively cooking. An administrator needed to manage the same content, including recipes with different numbers of ingredients, steps, and images. For visitors, the main challenge was keeping information from feeling scattered. Someone moving between the screen and cooking should not have to reread a large block of text every time they look back.</p>

            <h3>Goals</h3>
            <ul>
                <li>Make recipes quick to find through search and category filtering.</li>
                <li>Keep ingredients and quantities easy to scan while cooking.</li>
                <li>Make each cooking step visually distinct so it is easy to find your place again.</li>
                <li>Use step images to make the current task and what comes next easier to understand.</li>
                <li>Allow an authenticated administrator to add, edit, and delete recipes without directly editing the database.</li>
                <li>Keep the admin experience manageable even when a recipe has a lot of ingredients, steps, and images.</li>
            </ul>
        </section>
        <section class="case-study-section">
            <h2>Process and Insight</h2>
            <p>The database was one of the parts of the project that changed the most. The schema went through several versions before I settled on separate tables for recipes, categories, ingredients, steps, users, and supporting data. Ingredients and steps especially made more sense as their own records because every recipe has a different number of them. It took some trial and error, but the final structure made the rest of the application easier to work with.</p>

            <figure>
                <img
                    src="images/case-study/db.png"
                    alt="phpMyAdmin showing the tables used to organize the cookbook database"
                >
                <figcaption>
                    The database evolved throughout development as the relationships
                    between recipes and their supporting content became clearer.
                </figcaption>
            </figure>

            <p>Building the admin forms also showed how much small interactions affect usability. The first version added ingredients and steps through PHP, which submitted and reloaded the entire form. This moved the page away from where the administrator was working, and once image uploads were added, a reload also cleared selected files.</p>

            <figure class="process-screenshot">
                <img
                    src="images/case-study/edit-recipe.png"
                    alt="Edit Recipe interface showing editable ingredient fields and image controls"
                >
                <figcaption>
                    The admin form supports recipes with a variable number of
                    ingredients instead of limiting every recipe to the same structure.
                </figcaption>
            </figure>

            <figure class="process-screenshot">
                <img
                    src="images/case-study/edit-recipe-steps.png"
                    alt="Edit Recipe interface showing numbered steps with current images and replacement upload controls"
                >
                <figcaption>
                    Step editing keeps instructions and their images together so the administrator can see what is already attached before replacing it.
                </figcaption>
            </figure>

            <p>I moved those interactions to JavaScript so new fields could be inserted directly into the existing form. This keeps the administrator in the same place and preserves images that have already been selected.</p>

            <h3>Handling Recipes with Different Amounts of Content</h3>
            <p>A related server-side decision was how to store recipes with different numbers of ingredients and steps. Instead of giving the recipe table a fixed number of columns, each ingredient and step is its own row connected to a recipe. PHP creates the recipe first, gets its ID, and then uses that ID when saving its related content.</p>

            <pre><code>$recipe_id = (int) mysqli_insert_id($conn);

foreach ($ingredient_names as $index => $ingredient_name) {
    $ingredient_name = trim((string) $ingredient_name);
}</code></pre>

            <p class="code-caption">Excerpt from <code>admin/add-recipe.php</code>.</p>

            <p>This structure lets the admin form support as many ingredients and steps as a recipe actually needs instead of assuming a fixed amount.</p>
        </section>
        <section class="case-study-section">
            <h2>Solution</h2>
            <p>The finished cookbook uses a simple, clean layout because I did not want the interface competing with the recipe itself. Visitors can search by name or filter by category, and the recipe cards rely heavily on food photography with only the information needed to choose what to open.</p>
            <p>Inside a recipe, ingredients and quantities are grouped together before the instructions begin. The instructions are then divided into clearly numbered steps, with each title, image, and set of directions kept together. Someone cooking is constantly looking away from the screen, so these visual landmarks make it much easier to look back and quickly find the current step.</p>

            <figure>
                <img
                    src="images/case-study/recipe-detail.png"
                    alt="Recipe detail page showing the recipe description, serving information, ingredients, and ingredient image"
                >
                <figcaption>Recipe information is separated into clear sections so ingredients and quantities can be referenced quickly.
                </figcaption>
            </figure>

            <figure>
                <img
                    src="images/case-study/recipe-detail-steps.png"
                    alt="Recipe detail page showing numbered cooking steps with instructions and corresponding images"
                >
                <figcaption>Numbered instructions and corresponding images make it easier to return to the correct step while cooking.</figcaption>
            </figure>

            <h3>Managing Recipes</h3>
            <p>The admin interface keeps the same related information together. Recipes, ingredients, steps, and images can be managed without directly editing the database. Ingredients and steps can be added without reloading the page, and existing images are shown before they are replaced.</p>

            <h3>Using the Cookbook on Smaller Screens</h3>
            <p>The layout also adapts to smaller screens, which matters because a phone is a realistic way to follow a recipe in the kitchen. Content moves into a single-column layout, images scale with the available space, and numbered steps stay visually separate. The goal is the same as on desktop: after looking away to cook, the user should be able to quickly recognize where they were.</p>
        </section>
        <section class="case-study-section">
            <h2>Results</h2>

            <p>The finished cookbook met the visitor goals: recipes can be narrowed down through search and filtering, ingredients are easy to scan, and numbered, image-supported instructions make it easier to return to the correct step. The admin goals were also met through authenticated CRUD controls and flexible ingredient and step editing without page reloads.</p>
            
            <p>If I started the project again, I would spend more time planning the database before development. The schema changed several times as I figured out how the different types of recipe data needed to relate to each other, and mapping those relationships earlier would have saved changes later.</p>
            <p>One thing I learned is that getting something to work and getting it to feel good to use are two different things. The original Add Ingredient and Add Step controls technically worked, but actually using the workflow made the problems with reloading and losing my place obvious. Testing the application as a user helped catch details that were easy to miss while only focusing on whether the PHP worked.</p>
        </section>
    </main>
</body>
</html>