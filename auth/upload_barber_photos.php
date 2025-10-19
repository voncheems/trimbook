<?php
header('Content-Type: application/json');
session_start();

try {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get user_id from POST
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if (empty($user_id)) {
        throw new Exception("Invalid user ID");
    }

    // Check if file was uploaded
    if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No file uploaded or upload error occurred");
    }

    $file = $_FILES['profile_photo'];

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WebP are allowed");
    }

    // Validate file size (5MB max)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        throw new Exception("File size exceeds 5MB limit");
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/profile_photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = 'barber_' . $user_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception("Failed to move uploaded file");
    }

    // Get old photo path to delete
    $old_photo_query = $conn->prepare("SELECT profile_photo FROM users WHERE user_id = ?");
    $old_photo_query->bind_param("i", $user_id);
    $old_photo_query->execute();
    $old_result = $old_photo_query->get_result();
    
    if ($old_result->num_rows > 0) {
        $old_row = $old_result->fetch_assoc();
        $old_photo = $old_row['profile_photo'];
        
        // Delete old photo if it exists and is not a placeholder
        if ($old_photo && file_exists('../' . $old_photo)) {
            unlink('../' . $old_photo);
        }
    }
    $old_photo_query->close();

    // Update database with new photo path
    $relative_path = 'uploads/profile_photos/' . $unique_filename;
    
    $update_query = $conn->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
    $update_query->bind_param("si", $relative_path, $user_id);

    if ($update_query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Photo uploaded successfully',
            'photo_path' => $relative_path
        ]);
    } else {
        // Delete the uploaded file if database update fails
        unlink($upload_path);
        throw new Exception("Failed to update database: " . $update_query->error);
    }

    $update_query->close();
    $conn->close();

} catch (Exception $e) {
    // Clean up uploaded file on error
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
