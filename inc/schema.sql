CREATE DATABASE IF NOT EXISTS cookbook
  CHARACTER SET utf8mb4;


-- AI: prompt "create an outline of the database schema based on the provided excel sheet"
-- ChatGPT, 2026-10-14
-- reviewed and modified to follow course conventions
USE cookbook;

CREATE TABLE recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  subtitle VARCHAR(180),
  category VARCHAR(60),
  servings TINYINT UNSIGNED,
  description TEXT,
  ingredient_count TINYINT UNSIGNED,
  step_count TINYINT UNSIGNED,
  main_image VARCHAR(255),
  ingredients_image VARCHAR(255),
  image_folder VARCHAR(255),
  source_pdf VARCHAR(255)
);

CREATE TABLE ingredients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  sort_order TINYINT UNSIGNED NOT NULL,
  quantity VARCHAR(30),
  unit VARCHAR(50),
  name VARCHAR(160) NOT NULL,
  full_text VARCHAR(255),
  source VARCHAR(50),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  step_number TINYINT UNSIGNED NOT NULL,
  step_title VARCHAR(160),
  instructions TEXT NOT NULL,
  image VARCHAR(255),
  image_retina VARCHAR(255),
  source VARCHAR(50),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  UNIQUE KEY uq_recipe_step (recipe_id, step_number)
);

CREATE TABLE tools (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  brand VARCHAR(120),
  description TEXT,
  image VARCHAR(255),
  image_2 VARCHAR(255),
  image_3 VARCHAR(255)
);

CREATE TABLE recipe_tools (
  recipe_id INT NOT NULL,
  tool_id INT NOT NULL,
  source VARCHAR(50),
  PRIMARY KEY (recipe_id, tool_id),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (tool_id) REFERENCES tools(id) ON DELETE CASCADE
);

CREATE TABLE images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  type VARCHAR(50) NOT NULL,
  step_number TINYINT UNSIGNED,
  label VARCHAR(160),
  size VARCHAR(50),
  file_name VARCHAR(255) NOT NULL,
  path VARCHAR(255) NOT NULL,
  original_file VARCHAR(255),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE extra_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  type VARCHAR(50) NOT NULL,
  step_number TINYINT UNSIGNED,
  label VARCHAR(160),
  size VARCHAR(50),
  file_name VARCHAR(255) NOT NULL,
  path VARCHAR(255) NOT NULL,
  original_file VARCHAR(255),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE tips (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  tip_title VARCHAR(160),
  tip_text TEXT,
  image VARCHAR(255),
  tip_type VARCHAR(50),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE data_notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  area VARCHAR(100),
  missing_data TEXT,
  status VARCHAR(100),
  resolution TEXT,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);


-- seed data
INSERT INTO recipes (
  slug,
  title,
  subtitle,
  category,
  servings,
  description,
  ingredient_count,
  step_count,
  main_image,
  ingredients_image,
  image_folder,
  source_pdf
) VALUES
(
  'ancho-orange-chicken',
  'Ancho-Orange Chicken',
  'with Kale Rice & Roasted Carrots',
  'Chicken',
  4,
  'We''re amping up chicken breasts with a glaze of smoky ancho chile paste and fresh orange juice in this recipe. On the side, roasted carrots and lightly creamy, golden raisin-studded rice perfectly accent the sweetness of the glaze.',
  11,
  6,
  'images/ancho-orange-chicken/dish.jpg',
  'images/ancho-orange-chicken/ingredients.png',
  'images/ancho-orange-chicken',
  'Recipe_Ancho-Orange_Chicken_with_Kale_Rice_Roasted_Carrots.pdf'
),
(
  'beef-medallions-mushroom-sauce',
  'Beef Medallions & Mushroom Sauce',
  'with Mashed Potatoes',
  'Beef',
  2,
  'In this easy recipe, inspired by steak Diane (an American restaurant classic), we''re searing beef medallions, then using the fond in the pan to make a rich, bright mushroom sauce for spooning on top. Garlic mashed potatoes and sautéed kale make for delicious, simple sides.',
  9,
  6,
  'images/beef-medallions-mushroom-sauce/dish.jpg',
  'images/beef-medallions-mushroom-sauce/ingredients.png',
  'images/beef-medallions-mushroom-sauce',
  'Recipe_Beef_Medallions_Mushroom_Sauce_with_Mashed_Potatoes.pdf'
);