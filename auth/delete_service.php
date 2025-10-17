<?php
session_start();
header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "trimbook");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    $service_id = intval($data['service_id'] ?? 0);
    
    if (!$service_id) {
        throw new Exception("Invalid service ID");
    }
    
    // Check if service exists
    $check = $conn->prepare("SELECT service_id FROM services WHERE service_id = ?");
    $check->bind_param("i", $service_id);
    $check->execute();
    
    if ($check->get_result()->num_rows === 0) {
        throw new Exception("Service not found");
    }
    $check->close();
    
    // Delete the service
    $delete = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $delete->bind_param("i", $service_id);
    
    if ($delete->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    } else {
        throw new Exception("Failed to delete service: " . $delete->error);
    }
    
    $delete->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
