<?php
session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Database connection
require_once '../includes/dbconfig.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $current_password = $input['current_password'] ?? '';
    $new_password = $input['new_password'] ?? '';
    $admin_id = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    // Get current password hash from database
    // Use $conn instead of $pdo if that's what your dbconfig.php uses
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ? AND user_type = 'admin'");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit();
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Current password is incorrect',
            'error' => 'incorrect_current_password'
        ]);
        exit();
    }
    
    // Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_password_hash, $admin_id);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
