<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['course_id'])) {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];

$stmt = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    header("Location: index.php?msg=already_enrolled");
    exit;
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
$stmt->bind_param("ii", $student_id, $course_id);

if ($stmt->execute()) {
    header("Location: index.php?msg=enrolled_success");
} else {
    header("Location: index.php?msg=enrolled_error");
}
$stmt->close();
?>
