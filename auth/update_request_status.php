<?php
// update_request_status.php
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

$action = isset($input['action']) ? $input['action'] : '';
$request_id = isset($input['request_id']) ? $input['request_id'] : 0;

// Validate inputs
if (!in_array($action, ['resolve', 'reopen', 'delete'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

if (!$request_id || !is_numeric($request_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    if ($action === 'delete') {
        // Log the action first (optional)
        $logStmt = $conn->prepare("
            INSERT INTO password_reset_actions 
            (request_id, admin_id, action, action_at) 
            VALUES (?, ?, 'deleted', NOW())
        ");
        $admin_id = 1; // Default admin ID since no session
        $logStmt->bind_param("ii", $request_id, $admin_id);
        $logStmt->execute();
        $logStmt->close();
        
        // Delete the request
        $stmt = $conn->prepare("DELETE FROM password_reset_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Request deleted successfully'
        ]);

    } elseif ($action === 'resolve') {
        // Mark as resolved
        $admin_id = 1; // Default admin ID since no session
        $stmt = $conn->prepare("
            UPDATE password_reset_requests 
            SET status = 'resolved', 
                resolved_at = NOW(),
                resolved_by = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $admin_id, $request_id);
        $stmt->execute();
        $stmt->close();

        // Log the action (optional)
        $logStmt = $conn->prepare("
            INSERT INTO password_reset_actions 
            (request_id, admin_id, action, action_at) 
            VALUES (?, ?, 'resolved', NOW())
        ");
        $logStmt->bind_param("ii", $request_id, $admin_id);
        $logStmt->execute();
        $logStmt->close();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Request marked as resolved'
        ]);

    } elseif ($action === 'reopen') {
        // Reopen the request
        $stmt = $conn->prepare("
            UPDATE password_reset_requests 
            SET status = 'pending',
                resolved_at = NULL,
                resolved_by = NULL
            WHERE id = ?
        ");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        // Log the action (optional)
        $admin_id = 1; // Default admin ID since no session
        $logStmt = $conn->prepare("
            INSERT INTO password_reset_actions 
            (request_id, admin_id, action, action_at) 
            VALUES (?, ?, 'reopened', NOW())
        ");
        $logStmt->bind_param("ii", $request_id, $admin_id);
        $logStmt->execute();
        $logStmt->close();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Request reopened'
        ]);
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log('Update request status error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}

$conn->close();
?>
