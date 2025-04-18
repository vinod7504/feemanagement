-- Create database if not exists
CREATE DATABASE IF NOT EXISTS fee_management;
USE fee_management;

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    admission_no VARCHAR(50) UNIQUE NOT NULL,
    year VARCHAR(20) NOT NULL,
    stream VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    image_path VARCHAR(255),
    branch VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create fees table
CREATE TABLE IF NOT EXISTS fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    ref_no VARCHAR(50) UNIQUE NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    year VARCHAR(20) NOT NULL,
    stream VARCHAR(50) NOT NULL,
    branch VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert default admin account (password: admin123)
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$8tDjcgyX.wP89yy0F8WIp.rvZH.zc91Zl0GS6.YVrGYHVdqZpV9Uy')
ON DUPLICATE KEY UPDATE id=id; 