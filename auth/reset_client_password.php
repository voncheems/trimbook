<?php
session_start();
require_once('../includes/dbconfig.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: ../pages/login_page.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboards/admin_manageClient.php");
    exit();
}

// Get form data
$user_id = $_POST['user_id'] ?? null;
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate input
if (!$user_id || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error_message'] = "All fields are required.";
    header("Location: ../dashboards/admin_manageClient.php");
    exit();
}

// Check if passwords match
if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "Passwords do not match.";
    header("Location: ../dashboards/admin_manageClient.php");
    exit();
}

// Check password length
if (strlen($new_password) < 6) {
    $_SESSION['error_message'] = "Password must be at least 6 characters long.";
    header("Location: ../dashboards/admin_manageClient.php");
    exit();
}

// Verify user exists and is a customer
$check_query = "SELECT user_id, first_name, last_name FROM users WHERE user_id = ? AND user_type = 'customer'";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Client not found.";
    header("Location: ../dashboards/admin_manageClient.php");
    exit();
}

$user = $result->fetch_assoc();
$check_stmt->close();

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in database
$update_query = "UPDATE users SET password = ? WHERE user_id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $hashed_password, $user_id);

if ($update_stmt->execute()) {
    $_SESSION['success_message'] = "Password successfully reset for " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . ".";
} else {
    $_SESSION['error_message'] = "Failed to reset password. Please try again.";
}

$update_stmt->close();
$conn->close();

header("Location: ../dashboards/admin_manageClient.php");
exit();
?>
