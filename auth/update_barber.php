<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database configuration
require_once '../includes/dbconfig.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['barber_id']) || !isset($data['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$barber_id = intval($data['barber_id']);
$user_id = intval($data['user_id']);
$specialization = isset($data['specialization']) ? trim($data['specialization']) : null;
$experience_years = isset($data['experience_years']) ? intval($data['experience_years']) : 0;

// Validate barber exists
$check_query = "SELECT barber_id FROM barbers WHERE barber_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $barber_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Barber not found']);
    exit();
}

// Update barber information
$update_query = "UPDATE barbers SET specialization = ?, experience_years = ? WHERE barber_id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("sii", $specialization, $experience_years, $barber_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Barber updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update barber: ' . $conn->error]);
}

$update_stmt->close();
$check_stmt->close();
$conn->close();
?>
