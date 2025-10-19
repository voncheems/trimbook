<?php
session_start();

// Include database configuration
require_once '../includes/dbconfig.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize and validate user inputs
    $first_name = trim(htmlspecialchars($_POST['first_name']));
    $last_name = trim(htmlspecialchars($_POST['last_name']));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $phone_no = preg_replace('/[^0-9]/', '', $_POST['phone_no']); // Remove non-numeric characters
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    $specialization = trim(htmlspecialchars($_POST['specialization'] ?? ''));
    $experience_years = intval($_POST['experience_years'] ?? 0);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $working_days = $_POST['working_days'] ?? [];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address";
        header("Location: ../dashboards/admin_add_barber.php");
        exit();
    }
    
    // Validate phone number (must be exactly 11 digits)
    if (strlen($phone_no) !== 11) {
        $_SESSION['error'] = "Phone number must be exactly 11 digits";
        header("Location: ../dashboards/admin_add_barber.php");
        exit();
    }
    
    // Validate working days
    if (empty($working_days)) {
        $_SESSION['error'] = "Please select at least one working day";
        header("Location: ../dashboards/admin_add_barber.php");
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Handle profile photo upload
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file type
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = "Only JPG, JPEG, and PNG files are allowed";
            header("Location: ../dashboards/admin_add_barber.php");
            exit();
        }
        
        // Validate file size (2MB max)
        if ($file_size > 2 * 1024 * 1024) {
            $_SESSION['error'] = "File size must be less than 2MB";
            header("Location: ../dashboards/admin_add_barber.php");
            exit();
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = '../uploads/profile_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $unique_filename = uniqid('barber_', true) . '.' . $file_ext;
        $upload_path = $upload_dir . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $profile_photo = 'uploads/profile_photos/' . $unique_filename;
        } else {
            $_SESSION['error'] = "Failed to upload profile photo";
            header("Location: ../dashboards/admin_add_barber.php");
            exit();
        }
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if email or username already exists
        $check_query = "SELECT user_id FROM users WHERE email = ? OR username = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $email, $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            throw new Exception("Email or username already exists");
        }
        
        // Insert into users table
        $user_query = "INSERT INTO users (first_name, last_name, email, phone_no, username, password, user_type, profile_photo) 
                       VALUES (?, ?, ?, ?, ?, ?, 'barber', ?)";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone_no, $username, $hashed_password, $profile_photo);
        
        if (!$user_stmt->execute()) {
            throw new Exception("Failed to create user account");
        }
        
        $user_id = $conn->insert_id;
        
        // Insert into barbers table
        $barber_query = "INSERT INTO barbers (user_id, specialization, experience_years) VALUES (?, ?, ?)";
        $barber_stmt = $conn->prepare($barber_query);
        $barber_stmt->bind_param("isi", $user_id, $specialization, $experience_years);
        
        if (!$barber_stmt->execute()) {
            throw new Exception("Failed to create barber profile");
        }
        
        $barber_id = $conn->insert_id;
        
        // Insert schedules for each working day
        $schedule_query = "INSERT INTO schedules (barber_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
        $schedule_stmt = $conn->prepare($schedule_query);
        
        foreach ($working_days as $day) {
            $schedule_stmt->bind_param("isss", $barber_id, $day, $start_time, $end_time);
            if (!$schedule_stmt->execute()) {
                throw new Exception("Failed to create schedule for $day");
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Success message
        $_SESSION['success'] = "Barber added successfully!";
        header("Location: ../dashboards/admin_allbarbers.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Delete uploaded photo if exists
        if ($profile_photo && file_exists('../' . $profile_photo)) {
            unlink('../' . $profile_photo);
        }
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../dashboards/admin_addBarber.php");
        exit();
    }
    
} else {
    // If not POST request, redirect to form
    header("Location: ../dashboards/admin_addBarber.php");
    exit();
}
?>
