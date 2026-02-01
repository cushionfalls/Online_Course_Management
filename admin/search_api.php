<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$query = trim($_GET['q'] ?? '');

if ($action === 'search_students') {
    $results = [];
    
    if (empty($query)) {
        $stmt = $conn->prepare("SELECT id, username, email, fullname, created_at FROM users WHERE role = 'student' ORDER BY fullname");
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $searchTerm = "%{$query}%";
        $stmt = $conn->prepare("SELECT id, username, email, fullname, created_at FROM users WHERE role = 'student' AND (fullname LIKE ? OR email LIKE ? OR username LIKE ?) ORDER BY fullname");
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
    
    echo json_encode($results);
    
} elseif ($action === 'search_teachers') {
    $results = [];
    
    if (empty($query)) {
        $stmt = $conn->prepare("SELECT id, fullname, email, role FROM users WHERE role = 'teacher' ORDER BY fullname");
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $searchTerm = "%{$query}%";
        $stmt = $conn->prepare("SELECT id, fullname, email, role FROM users WHERE role = 'teacher' AND (fullname LIKE ? OR email LIKE ?) ORDER BY fullname");
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
    
    echo json_encode($results);
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}
?>
