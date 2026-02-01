<?php
$servername = "localhost";
$user       = "root";
$password   = "";
$db         = "checkassign";

$conn = new mysqli($servername, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$table_user = "CREATE TABLE IF NOT EXISTS users (
    id          INT(11) AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) UNIQUE NOT NULL,
    email       VARCHAR(100) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    fullname    VARCHAR(100) NOT NULL,
    role        ENUM('admin', 'student', 'teacher') DEFAULT 'student',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$course_table = "CREATE TABLE IF NOT EXISTS courses (
    course_id     INT(11) AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(100) NOT NULL,
    description   TEXT,
    instructor_id INT(11) NULL,
    category      VARCHAR(50) DEFAULT 'General',
    level         ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
)";


$table_assignment = "CREATE TABLE IF NOT EXISTS assignments (
    assignment_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    course_id     INT(11) NOT NULL,
    title         VARCHAR(100) NOT NULL,
    description   TEXT,
    due_date      DATETIME DEFAULT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
)";

$conn->query($table_user)       or die("Users table error: " . $conn->error);
$conn->query($course_table)     or die("Courses table error: " . $conn->error);
$conn->query($table_assignment) or die("Assignments table error: " . $conn->error);

$check_col = $conn->query("SHOW COLUMNS FROM courses LIKE 'category'");
if ($check_col->num_rows == 0) {
    $conn->query("ALTER TABLE courses ADD COLUMN category VARCHAR(50) DEFAULT 'General'");
}
$check_col_lvl = $conn->query("SHOW COLUMNS FROM courses LIKE 'level'");
if ($check_col_lvl->num_rows == 0) {
    $conn->query("ALTER TABLE courses ADD COLUMN level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner'");
}

?>