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
$phone_no = $conn->real_escape_string($data['phone_no']);
$specialization = $conn->real_escape_string($data['specialization']);
$experience_years = intval($data['experience_years']);
$schedule_days = $data['schedule_days'] ?? [];
$start_time = $data['start_time'];
$end_time = $data['end_time'];

// Start transaction
$conn->begin_transaction();

try {
    // Update users table
    $update_user = "UPDATE users SET phone_no = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_user);
    $stmt->bind_param('si', $phone_no, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update barbers table
    $update_barber = "UPDATE barbers SET specialization = ?, experience_years = ? WHERE barber_id = ?";
    $stmt = $conn->prepare($update_barber);
    $stmt->bind_param('sii', $specialization, $experience_years, $barber_id);
    $stmt->execute();
    $stmt->close();

    // Update schedules
    // First, delete existing schedules
    $delete_schedules = "DELETE FROM schedules WHERE barber_id = ?";
    $stmt = $conn->prepare($delete_schedules);
    $stmt->bind_param('i', $barber_id);
    $stmt->execute();
    $stmt->close();

    // Insert new schedules
    if (!empty($schedule_days) && !empty($start_time) && !empty($end_time)) {
        $insert_schedule = "INSERT INTO schedules (barber_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_schedule);
        
        foreach ($schedule_days as $day) {
            $stmt->bind_param('isss', $barber_id, $day, $start_time, $end_time);
            $stmt->execute();
        }
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Barber updated successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating barber: ' . $e->getMessage()]);
}

$conn->close();
?>
