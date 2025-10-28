<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/dbconfig.php';

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$user_id = intval($data['user_id']);
$new_password = $data['new_password'];

// Validate password
if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

// Hash the password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$query = "UPDATE users SET password = ? WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $hashed_password, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error changing password']);
}

$stmt->close();
$conn->close();
?>
