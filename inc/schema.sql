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
