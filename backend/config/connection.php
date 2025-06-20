<?php
$host = "localhost";
$user = "root";
$pass = ""; // your password here
$dbname = "school_system";

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create DB if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // echo "Database created or already exists.<br>";
} else {
    die("Database creation failed: " . $conn->error);
}

// Select DB
$conn->select_db($dbname);
?>
