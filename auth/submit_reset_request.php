<?php
// submit_reset_request.php
require_once '../includes/dbconfig.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.html?error=invalid_request');
    exit();
}

// Get and sanitize input
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

// Validate email
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: forgot_password.html?error=invalid_email');
    exit();
}

// Get additional info for security tracking
$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// Check for duplicate recent requests (within last 24 hours)
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM password_reset_requests 
    WHERE email = ? 
    AND submitted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    AND status = 'pending'
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($result['count'] > 0) {
    $conn->close();
    header('Location: ../pages/forgot_password.html?error=duplicate_request');
    exit();
}

// Insert the reset request
$stmt = $conn->prepare("
    INSERT INTO password_reset_requests 
    (email, phone, status, ip_address, user_agent, submitted_at) 
    VALUES (?, ?, 'pending', ?, ?, NOW())
");

$phone_value = $phone ?: null;
$stmt->bind_param("ssss", $email, $phone_value, $ip_address, $user_agent);

if ($stmt->execute()) {
    $stmt->close();
    
    // Optional: Send notification email to admin
    if (defined('ADMIN_EMAIL')) {
        $admin_email = ADMIN_EMAIL;
        $subject = 'New Password Reset Request - TrimBook';
        $message = "A new password reset request has been submitted.\n\n";
        $message .= "Email: " . $email . "\n";
        $message .= "Phone: " . ($phone ?: 'Not provided') . "\n";
        $message .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $message .= "IP Address: " . $ip_address . "\n\n";
        $message .= "Please review this request in the admin panel.";
        
        $headers = "From: noreply@trimbook.com\r\n";
        $headers .= "Reply-To: noreply@trimbook.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        @mail($admin_email, $subject, $message, $headers);
    }
    
    $conn->close();
    header('Location: ../pages/forgot_password.html?success=sent');
    exit();
} else {
    error_log('Password reset request error: ' . $conn->error);
    $stmt->close();
    $conn->close();
    header('Location: ../pages/forgot_password.html?error=failed');
    exit();
}
?>
