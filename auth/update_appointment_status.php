<?php
session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'barber') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once '../includes/dbconfig.php';

// Validate input
if (!isset($_POST['appointment_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$appointment_id = intval($_POST['appointment_id']);
$new_status = $_POST['status'];
$barber_user_id = $_SESSION['user_id'];

// Validate status
$valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

try {
    // Get barber_id from barbers table
    $barber_query = "SELECT barber_id FROM barbers WHERE user_id = ?";
    $stmt = $conn->prepare($barber_query);
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Barber profile not found']);
        exit();
    }
    
    $barber_row = $result->fetch_assoc();
    $barber_id = $barber_row['barber_id'];
    $stmt->close();
    
    // Verify the appointment belongs to this barber
    $verify_query = "SELECT appointment_id FROM appointments WHERE appointment_id = ? AND barber_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $appointment_id, $barber_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID or unauthorized access']);
        exit();
    }
    $stmt->close();
    
    // Update the appointment status
    $update_query = "UPDATE appointments SET status = ? WHERE appointment_id = ? AND barber_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sii", $new_status, $appointment_id, $barber_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment status']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>
