<?php
require_once __DIR__ . '/../backend/config/connection.php';

echo "<h3>Updating Database Schema...</h3>";

$sql_queries = [
    "
    -- Table for assigning teachers to specific subjects within sections
    CREATE TABLE IF NOT EXISTS `teacher_section_assignments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `teacher_id` INT NOT NULL,
        `section_id` INT NOT NULL,
        `subject_id` INT NOT NULL,
        `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
        UNIQUE (`teacher_id`, `section_id`, `subject_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ",
    "
    -- Table for storing the class schedule
    CREATE TABLE IF NOT EXISTS `schedules` (
        `id` INT AUTO_INCREMENT PRIMARY KEY, -- Already has ID
        `section_id` INT NOT NULL,
        `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday') NOT NULL,
        `period_number` INT NOT NULL,
        `subject_id` INT NOT NULL,
        `teacher_id` INT,
        `start_time` TIME NOT NULL,
        `end_time` TIME NOT NULL,
        `shift` ENUM('Morning', 'Afternoon') NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL,
        UNIQUE (`section_id`, `day_of_week`, `period_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    "
];

foreach ($sql_queries as $query) {
    preg_match('/CREATE TABLE IF NOT EXISTS `(.*?)`/', $query, $matches);
    $table_name = $matches[1] ?? 'table';
    if ($conn->query($query) === TRUE) {
        echo "<p style='color: green;'>Table '{$table_name}' created successfully or already exists.</p>";
    } else {
        echo "<p style='color: red;'>Error creating table '{$table_name}': " . $conn->error . "</p>";
    }
}

echo "<h4>Database schema update complete. You can now delete this file if you wish.</h4>";

$conn->close();
?>