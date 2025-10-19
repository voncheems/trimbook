<?php
header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get POST data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if ($data === null) {
        throw new Exception("Invalid JSON data");
    }
    
    $barber_id = isset($data['barber_id']) ? intval($data['barber_id']) : 0;
    $appointment_date = isset($data['appointment_date']) ? trim($data['appointment_date']) : '';
    
    if (empty($barber_id) || empty($appointment_date)) {
        throw new Exception("Missing required fields");
    }
    
    // Fetch all confirmed appointments for this barber on this date
    $query = $conn->prepare("
        SELECT appointment_time 
        FROM appointments 
        WHERE barber_id = ? 
        AND appointment_date = ? 
        AND status = 'confirmed'
    ");
    
    $query->bind_param("is", $barber_id, $appointment_date);
    $query->execute();
    $result = $query->get_result();
    
    $booked_times = [];
    while ($row = $result->fetch_assoc()) {
        // Convert 24-hour format (HH:MM:SS) to 12-hour format (H:MM AM/PM)
        $time_24 = $row['appointment_time'];
        $booked_times[] = convertTo12Hour($time_24);
    }
    
    echo json_encode([
        'success' => true,
        'booked_times' => $booked_times
    ]);
    
    $query->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function convertTo12Hour($time24) {
    $parts = explode(':', $time24);
    $hours = intval($parts[0]);
    $minutes = $parts[1];
    
    $period = $hours >= 12 ? 'PM' : 'AM';
    $hours = $hours % 12;
    $hours = $hours === 0 ? 12 : $hours;
    
    return $hours . ':' . $minutes . ' ' . $period;
}
?>
