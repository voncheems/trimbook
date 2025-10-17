<?php
session_start();
header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    
    $appointment_id = isset($data['appointment_id']) ? intval($data['appointment_id']) : 0;
    $new_status = isset($data['status']) ? trim($data['status']) : '';
    
    // Validate input
    if (!$appointment_id) {
        throw new Exception("Invalid appointment ID");
    }
    
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        throw new Exception("Invalid status. Must be: " . implode(', ', $valid_statuses));
    }
    
    // Check if appointment exists
    $check = $conn->prepare("SELECT appointment_id, status FROM appointments WHERE appointment_id = ?");
    $check->bind_param("i", $appointment_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Appointment not found");
    }
    
    $appointment = $result->fetch_assoc();
    $old_status = $appointment['status'];
    $check->close();
    
    // Update appointment status
    $update = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
    $update->bind_param("si", $new_status, $appointment_id);
    
    if ($update->execute()) {
        // If status changed to 'completed', also log it to reports
        if ($new_status === 'completed' && $old_status !== 'completed') {
            logCompletedAppointment($conn, $appointment_id);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment status updated successfully',
            'old_status' => $old_status,
            'new_status' => $new_status
        ]);
    } else {
        throw new Exception("Failed to update appointment: " . $update->error);
    }
    
    $update->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Function to log completed appointments (for reports)
function logCompletedAppointment($conn, $appointment_id) {
    try {
        // Get appointment details
        $query = $conn->prepare("
            SELECT 
                a.appointment_id,
                a.customer_user_id,
                a.barber_id,
                a.service_id,
                a.appointment_date,
                a.appointment_time,
                s.price
            FROM appointments a
            JOIN services s ON a.service_id = s.service_id
            WHERE a.appointment_id = ?
        ");
        
        $query->bind_param("i", $appointment_id);
        $query->execute();
        $result = $query->get_result();
        
        if ($result->num_rows > 0) {
            $apt = $result->fetch_assoc();
            
            // Insert into reports table (if it exists, or you can create one)
            // For now, we'll just log it - you can create a reports table later
            error_log("APPOINTMENT COMPLETED: ID={$appointment_id}, Barber ID={$apt['barber_id']}, Revenue=₱{$apt['price']}, Date={$apt['appointment_date']}");
        }
        
        $query->close();
    } catch (Exception $e) {
        error_log("Error logging completed appointment: " . $e->getMessage());
    }
}
?>
