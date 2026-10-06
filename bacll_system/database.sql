CREATE DATABASE IF NOT EXISTS bac_system;
USE bac_system;

-- Users (both students and admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student grade submissions
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    track ENUM('science','social') NOT NULL,
    math DECIMAL(5,2) DEFAULT NULL,
    biology DECIMAL(5,2) DEFAULT NULL,
    chemistry DECIMAL(5,2) DEFAULT NULL,
    physics DECIMAL(5,2) DEFAULT NULL,
    khmer DECIMAL(5,2) DEFAULT NULL,
    history DECIMAL(5,2) DEFAULT NULL,
    geography DECIMAL(5,2) DEFAULT NULL,
    civics DECIMAL(5,2) DEFAULT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default admin (password: admin123)
INSERT INTO users (username, password, role)
VALUES ('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdBESfWr0EXQkQ5Uu', 'admin');