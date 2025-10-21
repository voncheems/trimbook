<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/dbconfig.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$barber_id = $input['barber_id'] ?? null;
$user_id = $input['user_id'] ?? null;
$phone_no = $input['phone_no'] ?? null;
$specialization = $input['specialization'] ?? null;
$experience_years = $input['experience_years'] ?? 0;
$schedule_days = $input['schedule_days'] ?? [];
$start_time = $input['start_time'] ?? null;
$end_time = $input['end_time'] ?? null;

if (!$barber_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $conn->begin_transaction();

    // Update users table (phone number)
    $stmt = $conn->prepare("UPDATE users SET phone_no = ? WHERE user_id = ?");
    $stmt->bind_param("si", $phone_no, $user_id);
    $stmt->execute();

    // Update barbers table (specialization and experience)
    $stmt = $conn->prepare("UPDATE barbers SET specialization = ?, experience_years = ? WHERE barber_id = ?");
    $stmt->bind_param("sii", $specialization, $experience_years, $barber_id);
    $stmt->execute();

    // Update schedule - delete existing and insert new
    $stmt = $conn->prepare("DELETE FROM schedules WHERE barber_id = ?");
    $stmt->bind_param("i", $barber_id);
    $stmt->execute();

    // Insert new schedules if days are selected
    if (!empty($schedule_days) && $start_time && $end_time) {
        $stmt = $conn->prepare("INSERT INTO schedules (barber_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        
        foreach ($schedule_days as $day) {
            $stmt->bind_param("isss", $barber_id, $day, $start_time, $end_time);
            $stmt->execute();
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Barber updated successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
