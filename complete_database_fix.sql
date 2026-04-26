-- COMPLETE DATABASE FIX FOR INFINITYFREE
-- Run this in phpMyAdmin SQL tab

-- Drop all tables first
DROP TABLE IF EXISTS family_members;
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS event_registrations;
DROP TABLE IF EXISTS prayer_requests;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS contributions;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS families;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'pastor', 'treasurer', 'secretary', 'volunteer') DEFAULT 'volunteer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create members table with ALL columns
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('Male', 'Female'),
    marital_status ENUM('Single', 'Married', 'Widowed', 'Divorced'),
    membership_status ENUM('Active', 'Inactive', 'Visitor', 'New Convert') DEFAULT 'Visitor',
    baptism_date DATE,
    join_date DATE,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create families table
CREATE TABLE families (
    id INT PRIMARY KEY AUTO_INCREMENT,
    family_name VARCHAR(100) NOT NULL,
    head_of_family INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (head_of_family) REFERENCES members(id)
);

-- Create family_members table
CREATE TABLE family_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    family_id INT,
    member_id INT,
    relationship VARCHAR(50),
    FOREIGN KEY (family_id) REFERENCES families(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Create attendance table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    service_date DATE NOT NULL,
    service_type VARCHAR(50),
    present BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Create contributions table
CREATE TABLE contributions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    amount DECIMAL(10,2) NOT NULL,
    contribution_type ENUM('Tithe', 'Offering', 'Building Fund', 'Missions', 'Special Project') DEFAULT 'Offering',
    payment_method ENUM('Cash', 'Check', 'Online', 'Mobile Money') DEFAULT 'Cash',
    contribution_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL
);

-- Create events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(200),
    event_type VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create event_registrations table
CREATE TABLE event_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT,
    member_id INT,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Create groups table
CREATE TABLE `groups` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_name VARCHAR(100) NOT NULL,
    description TEXT,
    leader_id INT,
    meeting_day VARCHAR(20),
    meeting_time TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES members(id)
);

-- Create group_members table
CREATE TABLE group_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT,
    member_id INT,
    joined_date DATE,
    FOREIGN KEY (group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Create prayer_requests table
CREATE TABLE prayer_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    request TEXT NOT NULL,
    status ENUM('Open', 'Answered', 'Closed') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Create expenses table
CREATE TABLE expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    description VARCHAR(200) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    expense_date DATE NOT NULL,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recorded_by) REFERENCES users(id)
);

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, full_name, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@church.com', 'admin');
