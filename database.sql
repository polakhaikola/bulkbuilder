CREATE DATABASE IF NOT EXISTS gym_recipes_db;
USE gym_recipes_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'mod', 'writer', 'user') DEFAULT 'user',
    target_calories INT DEFAULT 0,
    target_protein DECIMAL(5,1) DEFAULT 0,
    target_carbs DECIMAL(5,1) DEFAULT 0,
    target_fats DECIMAL(5,1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    calories INT,
    protein DECIMAL(5,1),
    carbs DECIMAL(5,1),
    fats DECIMAL(5,1),
    image VARCHAR(255),
    author_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS daily_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    recipe_id INT NULL,
    food_name VARCHAR(200),
    quantity DECIMAL(5,2),
    calories INT,
    protein DECIMAL(5,1),
    carbs DECIMAL(5,1),
    fats DECIMAL(5,1),
    log_date DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id)
);

CREATE TABLE IF NOT EXISTS water_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    glasses INT DEFAULT 0,
    log_date DATE DEFAULT (CURRENT_DATE),
    UNIQUE KEY unique_user_date (user_id, log_date),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
