-- iSCSS Database Schema

CREATE DATABASE IF NOT EXISTS iscss_db;
USE iscss_db;

-- Users table (all user types)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    email VARCHAR(150),
    role ENUM('admin', 'student', 'teacher', 'office_staff') NOT NULL,
    registration_number VARCHAR(50) UNIQUE,
    faculty VARCHAR(100),
    department VARCHAR(100),
    position VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inquiries/Claims table
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    recipient_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('inquiry', 'claim') NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages/Replies table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inquiry_id INT NOT NULL,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tagged users in inquiries
CREATE TABLE inquiry_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inquiry_id INT NOT NULL,
    tagged_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (tagged_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin
INSERT INTO users (username, password, full_name, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');
-- Default password is 'password'
