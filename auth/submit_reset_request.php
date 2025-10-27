<?php
session_start();
require_once '../includes/dbconfig.php';

$errors = [];
$email_input = '';
$phone_input = '';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['reset_errors'] = ['Invalid request method'];
    header('Location: ../pages/forgot_password.php');
    exit();
}

// Get and sanitize input
$email_input = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$phone_input = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));

// Validate email with stricter regex
if (empty($email_input)) {
    $errors[] = 'Email address is required';
} elseif (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
} elseif (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email_input)) {
    $errors[] = 'Please enter a valid email address format';
} elseif (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email_input)) {
    $errors[] = 'Please enter a valid email address format';
} else {
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
    $stmt->bind_param("s", $email_input);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($result['count'] > 0) {
        $errors[] = 'A reset request for this email was already submitted in the last 24 hours. Please check your email or try again later.';
    } else {
        // Insert the reset request
        $stmt = $conn->prepare("
            INSERT INTO password_reset_requests 
            (email, phone, status, ip_address, user_agent, submitted_at) 
            VALUES (?, ?, 'pending', ?, ?, NOW())
        ");

        $phone_value = $phone_input ?: null;
        $stmt->bind_param("ssss", $email_input, $phone_value, $ip_address, $user_agent);

        if ($stmt->execute()) {
            $stmt->close();
            
            // Optional: Send notification email to admin
            if (defined('ADMIN_EMAIL')) {
                $admin_email = ADMIN_EMAIL;
                $subject = 'New Password Reset Request - TrimBook';
                $message = "A new password reset request has been submitted.\n\n";
                $message .= "Email: " . $email_input . "\n";
                $message .= "Phone: " . ($phone_input ?: 'Not provided') . "\n";
                $message .= "Time: " . date('Y-m-d H:i:s') . "\n";
                $message .= "IP Address: " . $ip_address . "\n\n";
                $message .= "Please review this request in the admin panel.";
                
                $headers = "From: noreply@trimbook.com\r\n";
                $headers .= "Reply-To: noreply@trimbook.com\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                
                @mail($admin_email, $subject, $message, $headers);
            }
            
            $conn->close();
            $_SESSION['reset_success'] = 'Your password reset request has been submitted successfully! An administrator will review your request and contact you shortly.';
            header('Location: ../pages/forgot_password.php');
            exit();
        } else {
            error_log('Password reset request error: ' . $conn->error);
            $errors[] = 'Failed to submit your request. Please try again later.';
            $stmt->close();
        }
    }
}

// If there are errors, store them and redirect back
$conn->close();
$_SESSION['reset_errors'] = $errors;
$_SESSION['reset_email'] = $email_input;
$_SESSION['reset_phone'] = $phone_input;
header('Location: ../pages/forgot_password.php');
exit();
?>
