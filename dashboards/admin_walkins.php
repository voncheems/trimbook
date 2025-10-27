<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../pages/login_page.php");
    exit();
}

// Get admin data from session
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_username = $_SESSION['admin_username'] ?? 'admin';

// Get initials for avatar
$initials = strtoupper(substr($admin_name, 0, 2));

// Include database configuration
require_once '../includes/dbconfig.php';

// Initialize variables
$customers = [];
$barbers = [];
$services = [];
$message = '';
$messageType = '';

// Fetch all customers
if (isset($conn) && $conn) {
    $result = $conn->query("SELECT user_id, first_name, last_name, phone_no FROM users WHERE user_type = 'customer' ORDER BY first_name, last_name");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
    }
    
    // Fetch all barbers
    $result = $conn->query("
        SELECT b.barber_id, u.first_name, u.last_name, b.specialization 
        FROM barbers b 
        JOIN users u ON b.user_id = u.user_id 
        ORDER BY u.first_name, u.last_name
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $barbers[] = $row;
        }
    }
    
    // Fetch all services
    $result = $conn->query("SELECT service_id, service_name, price FROM services ORDER BY service_name");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_appointment'])) {
    $first_name = trim($_POST['customer_first_name'] ?? '');
    $last_name = trim($_POST['customer_last_name'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $barber_id = $_POST['barber_id'] ?? '';
    $service_id = $_POST['service_id'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $status = $_POST['status'] ?? 'confirmed';
    
    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($phone) || empty($barber_id) || empty($service_id) || empty($appointment_date) || empty($appointment_time)) {
        $message = 'All required fields must be filled!';
        $messageType = 'error';
    } else {
        // Check if customer exists by phone number
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE phone_no = ? AND user_type = 'customer'");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Customer exists, get their ID
            $customer = $result->fetch_assoc();
            $customer_id = $customer['user_id'];
        } else {
            // Create new customer account
            $username = strtolower($first_name . substr($last_name, 0, 1) . rand(100, 999));
            $default_password = password_hash('walkin123', PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_no, username, password, user_type) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $username, $default_password);
            
            if ($stmt->execute()) {
                $customer_id = $conn->insert_id;
            } else {
                $message = 'Error creating customer account: ' . $conn->error;
                $messageType = 'error';
                $customer_id = null;
            }
        }
        $stmt->close();
        
        // If we have a customer ID, create the appointment
        if (isset($customer_id)) {
            $stmt = $conn->prepare("INSERT INTO appointments (customer_user_id, barber_id, service_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisss", $customer_id, $barber_id, $service_id, $appointment_date, $appointment_time, $status);
            
            if ($stmt->execute()) {
                $message = 'Walk-in appointment created successfully!';
                $messageType = 'success';
                // Clear form
                $_POST = array();
            } else {
                $message = 'Error creating appointment: ' . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Walk-in Appointments | TrimBook</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    body {
      font-family: 'Inter', sans-serif;
    }
    
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .sidebar {
      transform: translateX(-100%);
      transition: transform 0.3s ease-in-out;
    }

    .sidebar.open {
      transform: translateX(0);
    }

    .overlay {
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease-in-out;
    }

    .overlay.show {
      opacity: 1;
      pointer-events: auto;
    }

    .form-card {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      border: 1px solid rgba(102, 126, 234, 0.2);
    }

    .input-field {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }

    .input-field:focus {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(102, 126, 234, 0.5);
      outline: none;
    }

    /* Style for select dropdown options */
    .input-field option {
      background: #1f2937;
      color: #ffffff;
      padding: 10px;
    }

    .input-field option:hover {
      background: #374151;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .alert {
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

  <!-- Overlay -->
  <div id="overlay" class="overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-40" onclick="toggleSidebar()"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-80 bg-gradient-to-b from-gray-900 to-gray-950 border-r border-gray-800 z-50 overflow-y-auto">
    <div class="p-6">
      <!-- Sidebar Header -->
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black tracking-tight">ADMIN MENU</h2>
        <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Profile Section -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 mb-6">
        <div class="flex items-center space-x-4">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
            <?= htmlspecialchars($initials) ?>
          </div>
          <div>
            <h3 class="font-bold text-lg"><?= htmlspecialchars($admin_name) ?></h3>
            <p class="text-sm text-white/80">@<?= htmlspecialchars($admin_username) ?></p>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="space-y-2">
        <a href="admin_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>

        <a href="admin_allAppointment.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span>All Appointments</span>
        </a>

        <a href="admin_addBarber.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
          </svg>
          <span>Add Barber</span>
        </a>

        <a href="admin_allbarbers.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>Manage Barbers</span>
        </a>

        <a href="admin_manageClient.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>Manage Clients</span>
        </a>

        <a href="admin_manageservices.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span>Manage Services</span>
        </a>

        <a href="admin_reportpage.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Reports & Analytics</span>
        </a>

        <a href="admin_addcontact.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Manage Contact</span>
        </a>

        <a href="admin_feedback.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
          </svg>
          <span>Customer Feedback</span>
        </a>

        <a href="../dashboards/admin_walkins.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          <span>Add Appointment</span>
        </a>
      </nav>

      <!-- Logout Button -->
      <div class="mt-8 pt-6 border-t border-gray-800">
        <a href="../auth/logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span>Logout</span>
        </a>
      </div>
    </div>
  </aside>

  <!-- Header -->
  <header class="bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <div class="flex items-center space-x-4">
        <button onclick="toggleSidebar()" class="text-white hover:text-purple-400 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <a href="admin_dashboard.php" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-purple-500 text-sm">ADMIN</span></a>
      </div>
      <div class="flex items-center space-x-6">
        <span class="text-gray-400 text-sm hidden md:block">Welcome, <span class="text-white font-semibold"><?= htmlspecialchars($admin_name) ?></span></span>
        <a href="../auth/logout.php" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</a>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-4xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">Walk-in Appointments</h1>
        <p class="text-gray-400 text-lg">Create appointments for walk-in customers quickly</p>
      </div>

      <?php if ($message): ?>
        <div class="alert mb-8 p-4 rounded-xl <?= $messageType === 'success' ? 'bg-green-500/20 border border-green-500/50 text-green-400' : 'bg-red-500/20 border border-red-500/50 text-red-400' ?>">
          <div class="flex items-center space-x-3">
            <?php if ($messageType === 'success'): ?>
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            <?php else: ?>
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            <?php endif; ?>
            <span><?= htmlspecialchars($message) ?></span>
          </div>
        </div>
      <?php endif; ?>

      <!-- Appointment Form -->
      <div class="form-card rounded-3xl p-8">
        <form method="POST" action="" class="space-y-6">
          
          <!-- Customer Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="customer_first_name" class="block text-sm font-semibold mb-2 text-gray-300">Customer First Name</label>
              <input type="text" name="customer_first_name" id="customer_first_name" required class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="Enter first name">
            </div>
            
            <div>
              <label for="customer_last_name" class="block text-sm font-semibold mb-2 text-gray-300">Customer Last Name</label>
              <input type="text" name="customer_last_name" id="customer_last_name" required class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="Enter last name">
            </div>
          </div>

          <!-- Customer Contact -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="customer_phone" class="block text-sm font-semibold mb-2 text-gray-300">Phone Number</label>
              <input type="tel" name="customer_phone" id="customer_phone" required class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="09XXXXXXXXX" pattern="[0-9]{11}">
              <p class="text-xs text-gray-500 mt-1">11-digit phone number</p>
            </div>
            
            <div>
              <label for="customer_email" class="block text-sm font-semibold mb-2 text-gray-300">Email (Optional)</label>
              <input type="email" name="customer_email" id="customer_email" class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="customer@example.com">
            </div>
          </div>

          <!-- Barber Selection -->
          <div>
            <label for="barber_id" class="block text-sm font-semibold mb-2 text-gray-300">Select Barber</label>
            <select name="barber_id" id="barber_id" required class="input-field w-full px-4 py-3 rounded-xl text-white">
              <option value="">-- Choose a barber --</option>
              <?php foreach ($barbers as $barber): ?>
                <option value="<?= $barber['barber_id'] ?>">
                  <?= htmlspecialchars($barber['first_name'] . ' ' . $barber['last_name']) ?>
                  <?php if ($barber['specialization']): ?>
                    - <?= htmlspecialchars($barber['specialization']) ?>
                  <?php endif; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Service Selection -->
          <div>
            <label for="service_id" class="block text-sm font-semibold mb-2 text-gray-300">Select Service</label>
            <select name="service_id" id="service_id" required class="input-field w-full px-4 py-3 rounded-xl text-white">
              <option value="">-- Choose a service --</option>
              <?php foreach ($services as $service): ?>
                <option value="<?= $service['service_id'] ?>">
                  <?= htmlspecialchars($service['service_name']) ?> - ₱<?= number_format($service['price'], 2) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Date and Time Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Appointment Date -->
            <div>
              <label for="appointment_date" class="block text-sm font-semibold mb-2 text-gray-300">Appointment Date</label>
              <input type="date" name="appointment_date" id="appointment_date" required min="<?= date('Y-m-d') ?>" class="input-field w-full px-4 py-3 rounded-xl text-white">
            </div>

            <!-- Appointment Time -->
            <div>
              <label for="appointment_time" class="block text-sm font-semibold mb-2 text-gray-300">Appointment Time</label>
              <input type="time" name="appointment_time" id="appointment_time" required class="input-field w-full px-4 py-3 rounded-xl text-white">
            </div>
          </div>

          <!-- Status Selection -->
          <div>
            <label for="status" class="block text-sm font-semibold mb-2 text-gray-300">Appointment Status</label>
            <select name="status" id="status" required class="input-field w-full px-4 py-3 rounded-xl text-white">
              <option value="confirmed" selected>Confirmed</option>
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <!-- Submit Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="submit" name="create_appointment" class="btn-primary flex-1 px-8 py-4 rounded-xl font-bold text-white text-lg">
              Create Appointment
            </button>
            <a href="admin_dashboard.php" class="flex-1 px-8 py-4 rounded-xl font-bold text-center bg-gray-800/50 hover:bg-gray-800 text-white transition">
              Cancel
            </a>
          </div>

        </form>
      </div>

    </div>
  </main>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    }

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          overlay.classList.remove('show');
        }
      }
    });

    // Set minimum time based on current time if today is selected
    document.getElementById('appointment_date').addEventListener('change', function() {
      const selectedDate = new Date(this.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      const timeInput = document.getElementById('appointment_time');
      
      if (selectedDate.getTime() === today.getTime()) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeInput.min = `${hours}:${minutes}`;
      } else {
        timeInput.removeAttribute('min');
      }
    });
  </script>

</body>
</html>
