-- CREATE DATABASE IF NOT EXISTS Online_food_php;
USE Online_food_php;

-- Admin Table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    dark_mode TINYINT(1) DEFAULT 0
);

-- Dishes Table
CREATE TABLE IF NOT EXISTS dishes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    discount FLOAT DEFAULT 0.0,
    description TEXT
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    order_items TEXT NOT NULL, -- Stores JSON data (list of dishes & quantity)
    total_price DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (user_email) REFERENCES users(email)
);

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT NOT NULL
);

-- Insert default settings (with update if already exists)
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'PesUFood'),
('admin_email', 'admin@pesufood.com'),
('site_logo', ''),
('contact_info', ''),
('dark_mode', '0'),
('maintenance_mode', '0')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO admin (email, password)
VALUES (
  'admin@pesufood.com',
  'PASTE YOUR HASHED PASSWORD'
);