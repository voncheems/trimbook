
<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

$customer_user_id = $_SESSION['user_id'];

try {
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get POST data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    // Debug log
    error_log("Raw input: " . $input);
    error_log("Decoded data: " . json_encode($data));
    
    if ($data === null) {
        throw new Exception("Invalid JSON data");
    }
    
    $barber_id = isset($data['barber_id']) ? intval($data['barber_id']) : 0;
    $service_id = isset($data['service_id']) ? intval($data['service_id']) : 0;
    $appointment_date = isset($data['appointment_date']) ? trim($data['appointment_date']) : '';
    $appointment_time = isset($data['appointment_time']) ? trim($data['appointment_time']) : '';
    
    // Debug log
    error_log("barber_id: $barber_id, service_id: $service_id, date: $appointment_date, time: $appointment_time");
    
    // Validate input
    if (empty($barber_id) || empty($service_id) || empty($appointment_date) || empty($appointment_time)) {
        throw new Exception("Invalid input data - Missing fields. barber_id: $barber_id, service_id: $service_id, date: $appointment_date, time: $appointment_time");
    }
    
    // Validate date format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointment_date)) {
        throw new Exception("Invalid date format");
    }
    
    // Validate time format (HH:MM:SS)
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $appointment_time)) {
        throw new Exception("Invalid time format");
    }
    
    // Check if barber exists
    $barber_check = $conn->prepare("SELECT barber_id FROM barbers WHERE barber_id = ?");
    $barber_check->bind_param("i", $barber_id);
    $barber_check->execute();
    if ($barber_check->get_result()->num_rows === 0) {
        throw new Exception("Barber not found");
    }
    $barber_check->close();
    
    // Check if service exists
    $service_check = $conn->prepare("SELECT service_id FROM services WHERE service_id = ?");
    $service_check->bind_param("i", $service_id);
    $service_check->execute();
    if ($service_check->get_result()->num_rows === 0) {
        throw new Exception("Service not found");
    }
    $service_check->close();
    
    // Check if time slot is available (only confirmed appointments block slots)
    $availability_check = $conn->prepare("
        SELECT appointment_id FROM appointments 
        WHERE barber_id = ? 
        AND appointment_date = ? 
        AND appointment_time = ? 
        AND status = 'confirmed'
    ");
    $availability_check->bind_param("iss", $barber_id, $appointment_date, $appointment_time);
    $availability_check->execute();
    if ($availability_check->get_result()->num_rows > 0) {
        throw new Exception("This time slot is already booked");
    }
    $availability_check->close();
    
    // Insert appointment with 'confirmed' status
    $insert_query = $conn->prepare("
        INSERT INTO appointments (customer_user_id, barber_id, service_id, appointment_date, appointment_time, status)
        VALUES (?, ?, ?, ?, ?, 'confirmed')
    ");
    
    $insert_query->bind_param("iiiss", $customer_user_id, $barber_id, $service_id, $appointment_date, $appointment_time);
    
    if ($insert_query->execute()) {
        $appointment_id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment confirmed successfully',
            'appointment_id' => $appointment_id
        ]);
    } else {
        throw new Exception("Failed to create appointment: " . $insert_query->error);
    }
    
    $insert_query->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
