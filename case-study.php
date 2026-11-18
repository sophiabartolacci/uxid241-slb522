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

            <p>The Cookbook is a searchable recipe library designed to make recipe information easier to reference while actively cooking. Instead of making users search through scattered information, each recipe organizes ingredients, quantities, instructions, and images into clear sections that make it easier to see what is needed and what comes next.</p>
            <p>Behind the public recipe library is an authenticated admin area for maintaining the collection without directly editing the database. The project combines a relational MySQL database with PHP, JavaScript, and server-side validation to support recipe browsing, search and filtering, recipe management, and image uploads while keeping the interface simple and focused on the content.</p>
        </section>
        <section class="case-study-section">
            <h2>Context and Challenge</h2>
            <p>The cookbook is a recipe library designed around actually using a recipe while cooking, not just reading one beforehand. Recipe information can easily feel scattered between ingredients, quantities, instructions, and images. When someone is going back and forth between cooking and looking at the screen, they should be able to quickly find where they left off and see what comes next. The goal was to organize everything clearly enough that the page is easy to jump back into without having to reread a large block of information.</p>
            <p>The project was developed independently over one term and needed to work for two different types of users. Visitors needed a simple way to browse, search, and filter the recipe collection and then follow a recipe while cooking. An administrator needed a separate way to maintain that same content, including recipes with different numbers of ingredients, steps, and images.</p>

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
            <p>The project started with organizing the recipe data into a relational database. This ended up being one of the parts of the project that changed the most during development. The schema went through several versions before I settled on separate tables for recipes, categories, ingredients, steps, users, and other supporting data. Ingredients and steps especially made more sense as their own records because every recipe has a different number of them. Getting to that structure took some trial and error, but it made the rest of the application much easier to work with.</p>

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

            <p>Building the admin forms also showed how small interactions can have a large effect on usability. Ingredients and steps need to be flexible because every recipe contains a different number of them. The first version allowed the administrator to add another field through PHP, but doing so submitted and reloaded the entire form. The page would move away from the administrator's current position, and adding image uploads introduced another problem because selected files were cleared after a reload.</p>

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

            <p>To improve that interaction, adding ingredients and steps was moved to JavaScript. New fields are inserted directly into the existing form instead of requesting a new page from the server. This keeps the administrator in the same place and preserves any images that have already been selected.</p>

            <h3>Validating Image Uploads</h3>
            <p>File uploads also required a server-side security decision. An uploaded file cannot be trusted just because its filename says it is an image. The upload helper checks the file's actual MIME type and only accepts image formats included in an allowlist before the file is stored. This keeps the validation on the server rather than relying only on the browser's file picker.</p>

            <pre><code>$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmp_name);

if (!isset($ext_map[$mime])) {
    throw new RuntimeException('Type not allowed');
}</code></pre>

            <p class="code-caption">Excerpt from <code>inc/functions.php</code>.</p>
        </section>
        <section class="case-study-section">
            <h2>Solution</h2>
            <p>The finished cookbook uses a simple, clean layout because I did not want the interface competing with the recipe itself. From the recipe library, visitors can search by recipe name or narrow the collection by category instead of scrolling through every recipe. The cards rely heavily on the food photography, with only the information needed to decide which recipe to open.</p>
            <p>Once a recipe is open, the layout follows the way someone would actually use it while cooking. The name, serving information, description, and ingredients come first so everything can be checked before starting. Ingredients and quantities stay together in their own section instead of being mixed into the instructions.</p>

            <figure>
                <img
                    src="images/case-study/recipe-detail.png"
                    alt="Recipe detail page showing the recipe description, serving information, ingredients, and ingredient image"
                >
                <figcaption>Recipe information is separated into clear sections so ingredients and quantities can be referenced quickly.
                </figcaption>
            </figure>

            <p>The instructions are broken into clearly numbered steps rather than one long block of directions. Each step keeps its title, instructions, and image together. This was important because someone cooking is constantly looking away from the screen. When they look back, the large step number, heading, and image give them multiple ways to quickly find their place again instead of rereading the instructions from the beginning.</p>

            <figure>
                <img
                    src="images/case-study/recipe-detail-steps.png"
                    alt="Recipe detail page showing numbered cooking steps with instructions and corresponding images"
                >
                <figcaption>Numbered instructions and corresponding images make it easier to return to the correct step while cooking.</figcaption>
            </figure>
            <p>The layout also adapts to smaller screens so the same information remains readable when the available space changes. This is especially useful for a recipe site because someone may be following a recipe from a phone in the kitchen rather than sitting at a desktop. Images scale with the page and the content stays in a single readable flow instead of depending on a wide desktop layout.</p>
            <p>The admin side follows the same goal of keeping related information together. Ingredients and steps can be added as needed, existing images can be previewed before being replaced, and the administrator can manage the collection without working directly in the database.</p>
        </section>
        <section class="case-study-section">
            <h2>Results</h2>

            <p>The finished cookbook meets the main goals established for both visitors and administrators. Visitors can search or filter the recipe library, scan ingredients and quantities in their own section, and move through clearly numbered instructions with images that help show what each step should look like. On the admin side, recipes can be created, updated, and deleted without making changes directly in the database. Ingredients and steps can also be added without reloading the form, making longer recipes easier to manage.</p>
            <p>One of the biggest lessons from the project was the importance of
                planning the database structure early. The schema changed several times as the relationships between recipes, ingredients, steps, images, and other data became clearer. If starting the project again, I would spend more time mapping those relationships before beginning development. That would reduce the amount of database and PHP code that needed to be changed later and provide a more stable foundation for the rest of the
                application. </p>

            <p>One thing I learned from this project is that getting something to work and getting it to feel good to use are two different things. The original Add Ingredient and Add Step controls technically worked, but having the page reload and move away from where I was editing became frustrating very quickly. Actually using the admin workflow made those smaller problems much easier to notice than they were while I was only focused on whether the PHP worked.</p>
        </section>
    </main>
</body>
</html>