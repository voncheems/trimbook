<?php
header('Content-Type: application/json');
require_once '../includes/dbconfig.php';

if (!isset($_GET['barber_id'])) {
    echo json_encode(['success' => false, 'message' => 'Barber ID required']);
    exit();
}

$barber_id = intval($_GET['barber_id']);

$query = "SELECT day_of_week, start_time, end_time FROM schedules WHERE barber_id = ? ORDER BY 
    CASE day_of_week
        WHEN 'Monday' THEN 1
        WHEN 'Tuesday' THEN 2
        WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4
        WHEN 'Friday' THEN 5
        WHEN 'Saturday' THEN 6
        WHEN 'Sunday' THEN 7
    END";
    
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $barber_id);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode(['success' => true, 'schedules' => $schedules]);
$stmt->close();
$conn->close();
?>
