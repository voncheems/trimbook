<?php
session_start();
require_once('../includes/dbconfig.php');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../pages/login_page.php");
    exit();
}

// Check if user_id is provided
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    $_SESSION['error_message'] = 'Invalid user ID provided.';
    header("Location: ../dashboards/admin_manageClient.php");
    exit();
}

$user_id = intval($_POST['user_id']);

try {
    // Start transaction
    $conn->begin_transaction();
    
    // First, delete all appointments associated with this user
    $delete_appointments = "DELETE FROM appointments WHERE customer_user_id = ?";
    $stmt = $conn->prepare($delete_appointments);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Then delete the user
    $delete_user = "DELETE FROM users WHERE user_id = ? AND user_type = 'customer'";
    $stmt = $conn->prepare($delete_user);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Check if user was actually deleted
    if ($stmt->affected_rows > 0) {
        // Commit transaction
        $conn->commit();
        $_SESSION['success_message'] = 'Client deleted successfully.';
    } else {
        // Rollback if no user was deleted
        $conn->rollback();
        $_SESSION['error_message'] = 'Client not found or could not be deleted.';
    }
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $_SESSION['error_message'] = 'Error deleting client: ' . $e->getMessage();
}

// Redirect back to manage clients page
header("Location: ../dashboards/admin_manageClient.php");
exit();
?>
