<?php
session_start();
header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get POST data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    // Debug logging
    error_log("Raw input: " . $input);
    error_log("Decoded data: " . json_encode($data));
    
    if ($data === null) {
        throw new Exception("Invalid JSON data");
    }
    
    $service_id = isset($data['service_id']) && !empty($data['service_id']) ? intval($data['service_id']) : 0;
    $service_name = isset($data['service_name']) ? trim($data['service_name']) : '';
    $description = isset($data['description']) ? trim($data['description']) : '';
    $price = isset($data['price']) ? floatval($data['price']) : 0;
    
    // Debug logging
    error_log("service_id: $service_id, service_name: $service_name, description: $description, price: $price");
    
    // Validate input
    if (empty($service_name)) {
        throw new Exception("Service name is required");
    }
    
    if ($price <= 0) {
        throw new Exception("Price must be greater than 0");
    }
    
    if ($service_id > 0) {
        // UPDATE existing service
        $update_query = $conn->prepare("
            UPDATE services 
            SET service_name = ?, description = ?, price = ?
            WHERE service_id = ?
        ");
        
        if (!$update_query) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $update_query->bind_param("ssdi", $service_name, $description, $price, $service_id);
        
        if ($update_query->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Service updated successfully',
                'service_id' => $service_id
            ]);
        } else {
            throw new Exception("Failed to update service: " . $update_query->error);
        }
        
        $update_query->close();
    } else {
        // INSERT new service
        $insert_query = $conn->prepare("
            INSERT INTO services (service_name, description, price)
            VALUES (?, ?, ?)
        ");
        
        if (!$insert_query) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $insert_query->bind_param("ssd", $service_name, $description, $price);
        
        if ($insert_query->execute()) {
            $new_service_id = $conn->insert_id;
            
            echo json_encode([
                'success' => true,
                'message' => 'Service created successfully',
                'service_id' => $new_service_id
            ]);
        } else {
            throw new Exception("Failed to create service: " . $insert_query->error);
        }
        
        $insert_query->close();
    }
    
    $conn->close();
    
} catch (Exception $e) {
    error_log("Error in save_service.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
