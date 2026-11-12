## Project Description
This project is a server-side web app for an online cookbook. By the final version, users will be able to browse and search for recipes, view recipe details including ingredients and instructions, and manage their own recipe collection. The project uses PHP and MySQL to store and retrieve recipe data.

## Local Setup
### Prerequisites
Before running the project locally, you will need:
1. A local PHP and MySQL development environment:
   1. macOS: MAMP
   2. Windows: XAMPP
2. A web browser
3. phpMyAdmin, which is included with MAMP and XAMPP

### Steps
1. Clone or download the repository
2. Place the project folder in your local web server's document root:
   - **macOS with MAMP:** Place the project in the MAMP `htdocs` folder.
   - **Windows with XAMPP:** Place the project in the XAMPP `htdocs` folder.
3. Open MAMP or XAMPP and start the Apache and MySQL servers.
4. Open phpMyAdmin and import schema.sql to create and populate the cookbook database
5.  Copy inc/config.example.php and rename it to inc/config.php
6.  Update config.php with the database host, port, username, and password for your local environment
7. Open the project through localhost in a web browser

## Admin Login
The project includes a seeded admin account in schema.sql.
- Email: admin@example.com
- Password: test

You can also create an account through the registration page.

## Search and Filter
The user-facing feature for the Beta milestone is recipe search and category filtering. On the main recipe library page, users can search for a recipe by name, filter recipes by category, or use both options together.

## AI Use
- ChatGPT — used for guidance with setting up MAMP
- ChatGPT — assisted with reorganizing the provided recipe data to match the database schema and preparing the data for import into phpMyAdmin.
- ChatGPT — assisted with troubleshooting SQL and phpMyAdmin import errors, including handling NULL values and foreign key constraints.
- ChatGPT — generated the initial CSS scaffold and responsive styling for the public recipe pages, including the recipe card and recipe detail layouts
- ChatGPT — assisted with keeping the page positioned at the newly added ingredient or step after the PHP form reloads.
- ChatGPT — assisted with reorganizing CSS selectors from least specific to most specific and alphabetizing properties within each selector.
