<?php
require_once __DIR__ . "/../backend/config/connection.php";

function createTable($name, $query) {
    global $conn;
    if ($conn->query($query) === TRUE) {
        echo "<p class='message success'>Table '<strong>$name</strong>' created successfully.</p>";
    } else {
        echo "<p class='message error'>Error creating table '<strong>$name</strong>': " . htmlspecialchars($conn->error) . "</p>";
    }
}

// USERS
createTable("users", "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'director', 'rep') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// STUDENTS
createTable("students", "
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    nationality VARCHAR(50) NOT NULL,
    religion VARCHAR(50),
    city VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    emergency_contact VARCHAR(20) NOT NULL,
    guardian1_name VARCHAR(100) NOT NULL,
    guardian1_relation VARCHAR(50) NOT NULL,
    guardian1_phone VARCHAR(20) NOT NULL,
    guardian2_name VARCHAR(100),
    guardian2_relation VARCHAR(50),
    guardian2_phone VARCHAR(20),
    section_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// TEACHERS
createTable("teachers", "
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    qualification VARCHAR(100)
)");

// SECTIONS
createTable("sections", "
CREATE TABLE IF NOT EXISTS sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade INT NOT NULL,
    stream ENUM('natural','social') DEFAULT NULL,
    name VARCHAR(20) NOT NULL,
    capacity INT NOT NULL
)");

// SUBJECTS
createTable("subjects", "
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    grade_level INT NOT NULL,
    stream ENUM('natural','social') DEFAULT NULL
)");

// SHIFTS
createTable("shifts", "
CREATE TABLE IF NOT EXISTS shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_start TIME NOT NULL,
    break_end TIME NOT NULL,
    week_type ENUM('odd','even') NOT NULL,
    assigned_grades VARCHAR(50) NOT NULL
)");

// CLASS ASSIGNMENTS
createTable("class_assignments", "
CREATE TABLE IF NOT EXISTS class_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL UNIQUE,
    section_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// TEACHER SECTION ASSIGNMENTS
createTable("teacher_section_assignments", "
CREATE TABLE IF NOT EXISTS teacher_section_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    section_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// SUBJECT ASSIGNMENTS
createTable("subject_assignments", "
CREATE TABLE IF NOT EXISTS subject_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    year INT NOT NULL
)");

// ATTENDANCE
createTable("attendance", "
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present','absent') NOT NULL,
    marked_by INT NOT NULL,
    marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// GRADES
createTable("grades", "
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    test FLOAT,
    assignment FLOAT,
    activity FLOAT,
    exercise_book FLOAT,
    midterm FLOAT,
    total FLOAT,
    final_exam FLOAT,
    updated_by INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->close();
?>
