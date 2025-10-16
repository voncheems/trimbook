<?php
session_start();

// Database configuration
require_once '../includes/dbconfig.php'; // Assumes you have a config file with DB connection

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get and sanitize input data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone_no = trim($_POST['phone_no'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$specialization = trim($_POST['specialization'] ?? '');
$experience_years = intval($_POST['experience_years'] ?? 0);

// Get schedule data from form
$working_days = $_POST['working_days'] ?? [];
$start_time = trim($_POST['start_time'] ?? '');
$end_time = trim($_POST['end_time'] ?? '');

// Validation
$errors = [];

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
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}

if ($experience_years < 0) {
    $errors[] = 'Experience years cannot be negative';
}

// Validate schedule
if (empty($working_days) || !is_array($working_days)) {
    $errors[] = 'Please select at least one working day';
}

if (empty($start_time)) {
    $errors[] = 'Start time is required';
}

if (empty($end_time)) {
    $errors[] = 'End time is required';
}

// Validate time format
if (!empty($start_time) && !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $start_time)) {
    $errors[] = 'Invalid start time format';
}

if (!empty($end_time) && !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $end_time)) {
    $errors[] = 'Invalid end time format';
}

// Validate days
$valid_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
foreach ($working_days as $day) {
    if (!in_array($day, $valid_days)) {
        $errors[] = 'Invalid working day selected';
        break;
    }
}

// If there are validation errors, return them
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(', ', $errors);
    header('Location: ../admin/add_new_barber.php');
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('Email already exists');
    }
    $stmt->close();

    // Check if username already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('Username already exists');
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_no, username, password, user_type) VALUES (?, ?, ?, ?, ?, ?, 'barber')");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone_no, $username, $hashed_password);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create user account');
    }
    
    $user_id = $stmt->insert_id;
    $stmt->close();

    // Insert into barbers table
    $stmt = $conn->prepare("INSERT INTO barbers (user_id, specialization, experience_years) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $specialization, $experience_years);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create barber profile');
    }
    
    $barber_id = $stmt->insert_id;
    $stmt->close();

    // Insert schedules for each working day
    $stmt = $conn->prepare("INSERT INTO schedules (barber_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
    
    $schedules_created = 0;
    foreach ($working_days as $day) {
        $stmt->bind_param("isss", $barber_id, $day, $start_time, $end_time);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create barber schedule for ' . $day);
        }
        $schedules_created++;
    }
    
    $stmt->close();

    // Commit transaction
    $conn->commit();

    // Set success message and redirect
    $_SESSION['success_message'] = "Barber added successfully! Created $schedules_created schedule entries.";
    header('Location: ../dashboards/admin_dashboard.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: ../dashboards/admin_addBarber.php');
    exit();
} finally {
    // Close connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
