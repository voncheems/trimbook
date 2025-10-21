<?php
session_start();

// Include database connection
require_once '../includes/dbconfig.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;

// Validate appointment_id
if ($appointment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
    exit;
}

try {
    // Check if appointment exists and belongs to the user
    $check_query = "
        SELECT a.appointment_id, a.status
        FROM appointments a
        WHERE a.appointment_id = ? AND a.customer_user_id = ?
    ";
    
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $check_stmt->bind_param("ii", $appointment_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
        $check_stmt->close();
        exit;
    }
    
    $appointment = $result->fetch_assoc();
    $check_stmt->close();
    
    // Check if appointment can be cancelled (only pending or confirmed)
    if (!in_array($appointment['status'], ['pending', 'confirmed'])) {
        echo json_encode(['success' => false, 'message' => 'This appointment cannot be cancelled']);
        exit;
    }
    
    // Update appointment status to cancelled
    $update_query = "
        UPDATE appointments 
        SET status = 'cancelled'
        WHERE appointment_id = ? AND customer_user_id = ?
    ";
    
    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $update_stmt->bind_param("ii", $appointment_id, $user_id);
    
    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows > 0) {
            $update_stmt->close();
            echo json_encode(['success' => true, 'message' => 'Appointment cancelled successfully']);
        } else {
            $update_stmt->close();
            echo json_encode(['success' => false, 'message' => 'Failed to update appointment']);
        }
    } else {
        $update_stmt->close();
        throw new Exception("Execute failed: " . $conn->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
