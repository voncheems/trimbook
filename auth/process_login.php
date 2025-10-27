<?php
session_start();
require_once('../includes/dbconfig.php');

$errors = [];
$login_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login_input) || empty($password)) {
        $errors[] = 'Username/email and password required';
    } else {
        // Search for user by either username or email
        $sql = "SELECT user_id, first_name, last_name, username, email, password, user_type FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $login_input, $login_input);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Password correct - set session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // If barber, fetch and set barber_id
                    if ($user['user_type'] === 'barber') {
                        $barber_sql = "SELECT barber_id FROM barbers WHERE user_id = ?";
                        $barber_stmt = $conn->prepare($barber_sql);
                        $barber_stmt->bind_param("i", $user['user_id']);
                        $barber_stmt->execute();
                        $barber_result = $barber_stmt->get_result();
                        
                        if ($barber_result->num_rows == 1) {
                            $barber = $barber_result->fetch_assoc();
                            $_SESSION['barber_id'] = $barber['barber_id'];
                        }
                        $barber_stmt->close();
                    }
                    
                    // If admin, set admin session vars
                    if ($user['user_type'] === 'admin') {
                        $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['admin_username'] = $user['username'];
                    }
                    
                    $stmt->close();
                    $conn->close();
                    
                    // Redirect based on user type
                    if ($user['user_type'] === 'admin') {
                        header('Location: ../dashboards/admin_dashboard.php');
                    } elseif ($user['user_type'] === 'barber') {
                        header('Location: ../dashboards/barber_dashboard.php');
                    } else {
                        header('Location: ../dashboards/client_dashboard.php');
                    }
                    exit;
                } else {
                    $errors[] = 'Invalid username/email or password';
                }
            } else {
                $errors[] = 'Invalid username/email or password';
            }
            
            $stmt->close();
        }
    }
    $conn->close();
}

// Store errors in session for the login page to display
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_input'] = $login_input;
}
?>
