<?php
// process_register.php
// Place this file in the auth/ folder
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../includes/dbconfig.php';

// Initialize error array
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    $user_type = 'customer'; // Always set as customer for signup
    
    // Validation
    if (empty($first_name)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!$terms) {
        $errors[] = 'You must agree to the Terms of Service';
    }
    
    // If no validation errors, proceed with database checks
    if (empty($errors)) {
        
        // Check if username already exists
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'Username already taken';
        }
        
        mysqli_stmt_close($stmt);
        
        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'Email already registered';
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // If still no errors, insert the user
    if (empty($errors)) {
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user into database
        $stmt = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, email, username, password, user_type, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $email, $username, $hashed_password, $user_type);
        
        if (mysqli_stmt_execute($stmt)) {
            // Registration successful
            $user_id = mysqli_insert_id($conn);
            
            // **OPTION 1 APPLIED HERE - Set session variables and redirect to client dashboard**
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = $user_type;
            $_SESSION['success_message'] = 'Welcome to TrimBook! Your account has been created successfully.';
            
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            // Redirect to client dashboard instead of login page
            header('Location: ../dashboards/client_dashboard.php');
            exit;
            
        } else {
            $errors[] = 'Registration failed: ' . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // If there are errors, store them in session and redirect back
    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'username' => $username
        ];
        
        mysqli_close($conn);
        header('Location: ../pages/signup_page.php');
        exit;
    }
    
} else {
    // If not POST request, redirect to register page
    header('Location: ../pages/signup_page.php');
    exit;
}
?>
