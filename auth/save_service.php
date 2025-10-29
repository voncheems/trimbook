<?php
session_start();
require_once('../includes/dbconfig.php');

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;
    $service_name = trim($_POST['service_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;

    // Validation
    if (empty($service_name)) {
        echo json_encode(['success' => false, 'message' => 'Service name is required']);
        exit();
    }

    if ($price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
        exit();
    }

    try {
        if ($service_id) {
            // Update existing service
            $stmt = $conn->prepare("UPDATE services SET service_name = ?, description = ?, price = ? WHERE service_id = ?");
            $stmt->bind_param("ssdi", $service_name, $description, $price, $service_id);
            $message = 'Service updated successfully';
        } else {
            // Insert new service
            $stmt = $conn->prepare("INSERT INTO services (service_name, description, price) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $service_name, $description, $price);
            $message = 'Service added successfully';
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
