<?php
header('Content-Type: application/json');
session_start();

// Include database connection
require_once '../includes/dbconfig.php';

// Log all data
error_log("POST data: " . print_r($_POST, true));
error_log("User ID: " . $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = (int)$_POST['appointment_id'];
$barber_id = (int)$_POST['barber_id'];
$rating = (int)$_POST['rating'];
$comment = $_POST['comment'] ?? '';

error_log("Values - App: $appointment_id, Barber: $barber_id, Rating: $rating, User: $user_id");

// Validate
if ($appointment_id <= 0 || $barber_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid values']);
    exit;
}

try {
    // Verify appointment exists and is completed
    $check = "SELECT appointment_id FROM appointments WHERE appointment_id = ? AND customer_user_id = ? AND status = 'completed'";
    $stmt = $conn->prepare($check);
    
    if (!$stmt) {
        throw new Exception("Prepare check failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $appointment_id, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute check failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Appointment not found or not completed");
    }
    $stmt->close();
    
    // Insert feedback
    $insert = "INSERT INTO feedback (appointment_id, customer_user_id, barber_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    
    if (!$stmt) {
        throw new Exception("Prepare insert failed: " . $conn->error);
    }
    
    $stmt->bind_param("iiiss", $appointment_id, $user_id, $barber_id, $rating, $comment);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute insert failed: " . $stmt->error);
    }
    
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Feedback saved']);
    
} catch (Exception $e) {
    error_log("Feedback error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
