<?php
// delete_request.php (Soft Delete)
session_start();
require_once '../includes/dbconfig.php';

// Set JSON header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$request_id = isset($input['request_id']) ? (int)$input['request_id'] : 0;

// Validate input
if (!$request_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit();
}

// Get admin ID from session, or use default
$admin_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Start transaction
$conn->begin_transaction();

try {
    // Soft delete - mark as deleted instead of removing from database
    $stmt = $conn->prepare("
        UPDATE password_reset_requests 
        SET deleted_at = NOW(),
            deleted_by = ?
        WHERE id = ? AND deleted_at IS NULL
    ");
    $stmt->bind_param("ii", $admin_id, $request_id);
    $stmt->execute();
    
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    if ($affected_rows === 0) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Request not found or already deleted'
        ]);
        exit();
    }

    // Log the action
    $logStmt = $conn->prepare("
        INSERT INTO password_reset_actions 
        (request_id, admin_id, action, notes, action_at) 
        VALUES (?, ?, 'deleted', 'Soft deleted by admin', NOW())
    ");
    $logStmt->bind_param("ii", $request_id, $admin_id);
    $logStmt->execute();
    $logStmt->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Request deleted successfully'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log('Soft delete request error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}

$conn->close();
?>
