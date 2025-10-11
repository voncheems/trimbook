<?php
// process_login.php
// Place this file in the auth/ folder
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../includes/dbconfig.php';

// Initialize error array
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize form data
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($username_or_email)) {
        $errors[] = 'Username or email is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // If no validation errors, proceed with authentication
    if (empty($errors)) {
        
        // Check if input is an email or username
        if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
            // Input is an email
            $stmt = mysqli_prepare($conn, "SELECT user_id, first_name, last_name, username, email, password, user_type FROM users WHERE email = ?");
        } else {
            // Input is a username
            $stmt = mysqli_prepare($conn, "SELECT user_id, first_name, last_name, username, email, password, user_type FROM users WHERE username = ?");
        }
        
        mysqli_stmt_bind_param($stmt, "s", $username_or_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // User found, verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['success_message'] = 'Welcome back, ' . $user['first_name'] . '!';
                
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                
                // Redirect based on user type
                if ($user['user_type'] === 'admin') {
                    header('Location: /trimbook/dashboards/admin_dashboard.php');
                } elseif ($user['user_type'] === 'barber') {
                    header('Location: /trimbook/dashboards/barber_dashboard.php');
                } else {
                    // Default to client dashboard for 'customer' or any other type
                    header('Location: /trimbook/dashboards/client_dashboard.php');
                }
                exit;
                
            } else {
                // Password is incorrect
                $errors[] = 'Invalid username/email or password';
            }
        } else {
            // User not found
            $errors[] = 'Invalid username/email or password';
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // If there are errors, store them in session and redirect back
    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        $_SESSION['login_username_or_email'] = $username_or_email;
        
        mysqli_close($conn);
        header('Location: ../pages/login.php');
        exit;
    }
    
} else {
    // If not POST request, redirect to login page
    header('Location: ../pages/login.php');
    exit;
}
?>
