
-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS toyworld_db;
USE toyworld_db;

-- إنشاء جدول المستخدمين
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إنشاء جدول الألعاب
CREATE TABLE toys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إنشاء جدول الطلبات
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    toy_id INT,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (toy_id) REFERENCES toys(id)
);

-- بيانات تجريبية للمستخدمين
INSERT INTO users (username, email, password) VALUES 
('waodi', 'waodi@example.com', '123456'),
('reem', 'reem@example.com', '123456');

-- بيانات تجريبية للألعاب
INSERT INTO toys (name, description, price, image) VALUES 
('Lego Classic', 'Colorful lego set', 150.00, 'lego.jpg'),
('Remote Car', 'Fast and fun remote car', 200.00, 'car.jpg'),
('Doll House', 'Complete doll house with furniture', 300.00, 'dollhouse.jpg');

ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT 0;