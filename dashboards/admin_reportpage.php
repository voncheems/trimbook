<?php
session_start();

// Get admin data from session
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_username = $_SESSION['admin_username'] ?? 'admin';

// Get initials for avatar
$initials = strtoupper(substr($admin_name, 0, 2));

// Include database configuration
require_once '../includes/dbconfig.php';

// Initialize report data
$report_data = [
    'appointments_today' => 0,
    'appointments_this_week' => 0,
    'appointments_this_month' => 0,
    'completed_appointments' => 0,
    'cancelled_appointments' => 0,
    'pending_appointments' => 0,
    'top_services' => [],
    'top_barbers' => [],
    'recent_appointments' => [],
    'appointments_by_month' => []
];

// Date ranges
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$month_start = date('Y-m-01');

if (isset($conn) && $conn) {
    // Appointments Today
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = '$today'");
    if ($result && $row = $result->fetch_assoc()) {
        $report_data['appointments_today'] = $row['count'];
    }
    
    // Appointments This Week
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date >= '$week_start'");
    if ($result && $row = $result->fetch_assoc()) {
        $report_data['appointments_this_week'] = $row['count'];
    }
    
    // Appointments This Month
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date >= '$month_start'");
    if ($result && $row = $result->fetch_assoc()) {
        $report_data['appointments_this_month'] = $row['count'];
    }
    
    // Appointments by Status
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'");
    if ($result && $row = $result->fetch_assoc()) {
        $report_data['completed_appointments'] = $row['count'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled'");
    if ($result && $row = $result->fetch_assoc()) {
        $report_data['cancelled_appointments'] = $row['count'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'");
    if ($result && $row = $result->fetch_assoc()) {
        $report_data['pending_appointments'] = $row['count'];
    }
    
    // Top 5 Services
    $result = $conn->query("
        SELECT s.service_name, COUNT(*) as booking_count
        FROM appointments a
        JOIN services s ON a.service_id = s.service_id
        GROUP BY s.service_id
        ORDER BY booking_count DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report_data['top_services'][] = $row;
        }
    }
    
    // Top 5 Barbers
    $result = $conn->query("
        SELECT u.first_name, u.last_name, COUNT(*) as appointment_count
        FROM appointments a
        JOIN barbers b ON a.barber_id = b.barber_id
        JOIN users u ON b.user_id = u.user_id
        WHERE a.status = 'completed'
        GROUP BY b.barber_id
        ORDER BY appointment_count DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report_data['top_barbers'][] = $row;
        }
    }
    
    // Recent Appointments (Last 10)
    $result = $conn->query("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
               u.first_name as customer_first, u.last_name as customer_last,
               ub.first_name as barber_first, ub.last_name as barber_last,
               s.service_name
        FROM appointments a
        JOIN users u ON a.customer_user_id = u.user_id
        JOIN barbers b ON a.barber_id = b.barber_id
        JOIN users ub ON b.user_id = ub.user_id
        JOIN services s ON a.service_id = s.service_id
        ORDER BY a.created_at DESC
        LIMIT 10
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report_data['recent_appointments'][] = $row;
        }
    }
    
    // Appointments by Month (Last 6 months)
    $result = $conn->query("
        SELECT DATE_FORMAT(a.appointment_date, '%Y-%m') as month,
               COUNT(*) as appointments
        FROM appointments a
        WHERE a.appointment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month DESC
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report_data['appointments_by_month'][] = $row;
        }
    }
}

// Helper function for status badge colors
function getStatusColor($status) {
    switch($status) {
        case 'completed': return 'bg-green-500/20 text-green-400 border-green-500/30';
        case 'confirmed': return 'bg-blue-500/20 text-blue-400 border-blue-500/30';
        case 'pending': return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
        case 'cancelled': return 'bg-red-500/20 text-red-400 border-red-500/30';
        default: return 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Analytics | TrimBook Admin</title>
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

    .stat-card {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      border: 1px solid rgba(102, 126, 234, 0.2);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      border-color: rgba(102, 126, 234, 0.4);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
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

    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background: white;
        color: black;
      }
    }
  </style>
  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    }
  </script>
</head>
<body class="bg-black text-white antialiased">

  <!-- Overlay -->
  <div id="overlay" class="overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-40 no-print" onclick="toggleSidebar()"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-80 bg-gradient-to-b from-gray-900 to-gray-950 border-r border-gray-800 z-50 overflow-y-auto no-print">
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
        <a href="../dashboards/admin_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>

        <a href="../dashboards/admin_allAppointment.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
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

        <a href="../dashboards/admin_allbarbers.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>Manage Barbers</span>
        </a>

        <a href="../dashboards/admin_manageClient.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>Manage Clients</span>
        </a>

        <a href="../dashboards/admin_manageservices.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span>Manage Services</span>
        </a>

        <a href="../dashboards/admin_reportpage.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Reports & Analytics</span>
        </a>

        <a href="../dashboards/admin_addcontact.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Manage Contacts</span>
        </a>

        <a href="../dashboards/admin_feedback.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
          </svg>
          <span>Customer Feedback</span>
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
  <header class="bg-black/80 backdrop-blur-lg border-b border-gray-800 no-print">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <div class="flex items-center space-x-4">
        <button onclick="toggleSidebar()" class="text-white hover:text-purple-400 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <a href="#" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-purple-500 text-sm">ADMIN</span></a>
      </div>
      <div class="flex items-center space-x-4">
        <button onclick="window.print()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg font-medium transition flex items-center space-x-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
          <span>Print Report</span>
        </button>
        <span class="text-black text-sm hidden md:block"><?= htmlspecialchars($admin_name) ?></span>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-7xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">Reports & Analytics</h1>
        <p class="text-gray-400 text-lg">Comprehensive overview of your barbershop performance</p>
        <p class="text-gray-500 text-sm mt-2">Generated on <?= date('F d, Y \a\t h:i A') ?></p>
      </div>

      <!-- Key Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="stat-card rounded-2xl p-6">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-400 text-sm font-medium">Today's Appointments</p>
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-black gradient-text"><?= $report_data['appointments_today'] ?></p>
        </div>

        <div class="stat-card rounded-2xl p-6">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-400 text-sm font-medium">This Week</p>
            <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-black gradient-text"><?= $report_data['appointments_this_week'] ?></p>
        </div>

        <div class="stat-card rounded-2xl p-6">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-400 text-sm font-medium">This Month</p>
            <div class="w-10 h-10 bg-gradient-to-br from-orange-600 to-red-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-black gradient-text"><?= $report_data['appointments_this_month'] ?></p>
        </div>
      </div>

      <!-- Appointment Status Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-green-700/30 rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-1">Completed</p>
              <p class="text-3xl font-bold text-green-400"><?= $report_data['completed_appointments'] ?></p>
            </div>
            <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-yellow-700/30 rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-1">Pending</p>
              <p class="text-3xl font-bold text-yellow-400"><?= $report_data['pending_appointments'] ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-red-700/30 rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-1">Cancelled</p>
              <p class="text-3xl font-bold text-red-400"><?= $report_data['cancelled_appointments'] ?></p>
            </div>
            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Services & Top Barbers -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        <!-- Top Services -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
            <h2 class="text-2xl font-bold">Top Services</h2>
          </div>
          <div class="p-8">
            <?php if (empty($report_data['top_services'])): ?>
              <p class="text-gray-400">No service data available yet.</p>
            <?php else: ?>
              <div class="space-y-4">
                <?php foreach ($report_data['top_services'] as $index => $service): ?>
                  <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                    <div class="flex items-center space-x-4">
                      <div class="w-10 h-10 bg-purple-600/20 rounded-full flex items-center justify-center">
                        <span class="text-purple-400 font-bold">#<?= $index + 1 ?></span>
                      </div>
                      <div>
                        <p class="font-semibold"><?= htmlspecialchars($service['service_name']) ?></p>
                        <p class="text-sm text-gray-400"><?= $service['booking_count'] ?> bookings</p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Top Barbers -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="bg-gradient-to-r from-green-600 to-teal-600 px-8 py-6">
            <h2 class="text-2xl font-bold">Top Barbers</h2>
          </div>
          <div class="p-8">
            <?php if (empty($report_data['top_barbers'])): ?>
              <p class="text-gray-400">No barber data available yet.</p>
            <?php else: ?>
              <div class="space-y-4">
                <?php foreach ($report_data['top_barbers'] as $index => $barber): ?>
                  <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                    <div class="flex items-center space-x-4">
                      <div class="w-10 h-10 bg-green-600/20 rounded-full flex items-center justify-center">
                        <span class="text-green-400 font-bold">#<?= $index + 1 ?></span>
                      </div>
                      <div>
                        <p class="font-semibold"><?= htmlspecialchars($barber['first_name'] . ' ' . $barber['last_name']) ?></p>
                        <p class="text-sm text-gray-400"><?= $barber['appointment_count'] ?> appointments</p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Appointments -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Recent Appointments</h2>
        </div>
        <div class="p-8">
          <?php if (empty($report_data['recent_appointments'])): ?>
            <p class="text-gray-400">No appointments yet.</p>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-gray-700">
                    <th class="text-left py-4 px-4 text-gray-400 font-medium">ID</th>
                    <th class="text-left py-4 px-4 text-gray-400 font-medium">Customer</th>
                    <th class="text-left py-4 px-4 text-gray-400 font-medium">Barber</th>
                    <th class="text-left py-4 px-4 text-gray-400 font-medium">Service</th>
                    <th class="text-left py-4 px-4 text-gray-400 font-medium">Date</th>
                    <th class="text-left py-4 px-4 text-gray-400 font-medium">Time</th>
                    <th class="text-center py-4 px-4 text-gray-400 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($report_data['recent_appointments'] as $appointment): ?>
                    <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition">
                      <td class="py-4 px-4 font-mono text-sm text-gray-400">#<?= $appointment['appointment_id'] ?></td>
                      <td class="py-4 px-4"><?= htmlspecialchars($appointment['customer_first'] . ' ' . $appointment['customer_last']) ?></td>
                      <td class="py-4 px-4"><?= htmlspecialchars($appointment['barber_first'] . ' ' . $appointment['barber_last']) ?></td>
                      <td class="py-4 px-4 text-gray-300"><?= htmlspecialchars($appointment['service_name']) ?></td>
                      <td class="py-4 px-4 text-gray-300"><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                      <td class="py-4 px-4 text-gray-300"><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></td>
                      <td class="py-4 px-4 text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border <?= getStatusColor($appointment['status']) ?>">
                          <?= ucfirst($appointment['status']) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-black/80 border-t border-gray-800 py-6 text-center text-gray-400 text-sm no-print">
    <p>&copy; <?= date('Y') ?> TrimBook. All rights reserved.</p>
  </footer>

</body>
</html>
