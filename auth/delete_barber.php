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

$barber_id = intval($data['barber_id']);
$user_id = intval($data['user_id']);

// Start transaction
$conn->begin_transaction();

try {
    // Get profile photo path before deleting
    $query = "SELECT profile_photo FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $photo_path = $result->fetch_assoc()['profile_photo'] ?? null;
    $stmt->close();

    // Delete from barbers table (this will cascade delete schedules due to foreign key)
    $delete_barber = "DELETE FROM barbers WHERE barber_id = ?";
    $stmt = $conn->prepare($delete_barber);
    $stmt->bind_param('i', $barber_id);
    $stmt->execute();
    $stmt->close();

    // Delete from users table (this will cascade delete appointments and feedback due to foreign key)
    $delete_user = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_user);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete profile photo if it exists
    if ($photo_path && file_exists('../' . $photo_path)) {
        unlink('../' . $photo_path);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Barber deleted successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error deleting barber: ' . $e->getMessage()]);
}

$conn->close();
?>
