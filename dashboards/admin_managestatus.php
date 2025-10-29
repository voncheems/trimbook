<?php
session_start();
require_once('../includes/dbconfig.php');

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

// Fetch all appointments from database
$appointments = [];
$db_error = null;

    $query = "
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            u_customer.first_name as customer_first_name,
            u_customer.last_name as customer_last_name,
            u_barber.first_name as barber_first_name,
            u_barber.last_name as barber_last_name,
            s.service_name,
            a.created_at
        FROM appointments a
        JOIN users u_customer ON a.customer_user_id = u_customer.user_id
        JOIN barbers b ON a.barber_id = b.barber_id
        JOIN users u_barber ON b.user_id = u_barber.user_id
        JOIN services s ON a.service_id = s.service_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
// Function to get status badge
function getStatusBadge($status) {
    $badges = [
        'pending' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500/30', 'label' => 'Pending'],
        'confirmed' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-400', 'border' => 'border-green-500/30', 'label' => 'Confirmed'],
        'completed' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'border-blue-500/30', 'label' => 'Completed'],
        'cancelled' => ['bg' => 'bg-red-500/20', 'text' => 'text-red-400', 'border' => 'border-red-500/30', 'label' => 'Cancelled']
    ];
    return $badges[$status] ?? $badges['pending'];
}

// Format date and time for display
function formatDateTime($date, $time) {
    $dateObj = new DateTime($date);
    return $dateObj->format('M d, Y') . ' at ' . date('g:i A', strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Appointments | TrimBook Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    body {
      font-family: 'Inter', sans-serif;
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

    .modal {
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal.show {
      display: flex;
      opacity: 1;
    }

    .appointment-row:hover {
      background-color: rgba(255, 255, 255, 0.05);
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

         <a href="admin_allAppointment.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
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

        <a href="../dashboards/admin_feedback.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
          </svg>
          <span>Password Resets</span>
        </a>

         <a href="../dashboards/admin_walkins.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
          </svg>
          <span>Add Appointment</span>
        </a>
        <a href="../dashboards/admin_editpass.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Change Admin Password</span>
        </a>
      </nav>

      <!-- Logout Button -->
      <div class="mt-8 pt-6 border-t border-gray-800">
        <button onclick="confirmLogout()" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span>Logout</span>
        </button>
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
        <button onclick="confirmLogout()" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</button>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-7xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">
          Manage Appointments
        </h1>
        <p class="text-gray-400 text-lg">View and update appointment statuses</p>
      </div>

      <!-- Error Message -->
      <?php if ($db_error): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($db_error) ?></p>
        </div>
      <?php endif; ?>

      <!-- Appointments Table -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">All Appointments (<?= count($appointments) ?>)</h2>
        </div>

        <!-- Table or Empty State -->
        <?php if (count($appointments) > 0): ?>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-700">
                <tr class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                  <th class="px-8 py-5 text-left">Client</th>
                  <th class="px-8 py-5 text-left">Barber</th>
                  <th class="px-8 py-5 text-left">Service</th>
                  <th class="px-8 py-5 text-left">Date & Time</th>
                  <th class="px-8 py-5 text-center">Status</th>
                  <th class="px-8 py-5 text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($appointments as $apt): ?>
                  <tr class="appointment-row border-b border-gray-800 transition">
                    <td class="px-8 py-6 font-medium"><?= htmlspecialchars($apt['customer_first_name'] . ' ' . $apt['customer_last_name']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= htmlspecialchars($apt['barber_first_name'] . ' ' . $apt['barber_last_name']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= htmlspecialchars($apt['service_name']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= formatDateTime($apt['appointment_date'], $apt['appointment_time']) ?></td>
                    <td class="px-8 py-6 text-center">
                      <?php $badge = getStatusBadge($apt['status']); ?>
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $badge['bg'] ?> <?= $badge['text'] ?> border <?= $badge['border'] ?>">
                        <?= $badge['label'] ?>
                      </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                      <button onclick="openStatusModal(<?= htmlspecialchars(json_encode($apt)) ?>)" class="text-blue-400 hover:text-blue-300 font-medium transition">
                        Update Status
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <!-- Empty State -->
          <div class="px-8 py-16 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 opacity-50">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <p class="text-gray-400 text-lg font-medium">No appointments yet</p>
            <p class="text-gray-500 text-sm mt-2">Appointments will appear here once clients book them</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center">
    <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
  </footer>

  <!-- Status Update Modal -->
  <div id="statusModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-md mx-6 max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 sticky top-0">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold">Update Status</h2>
          <button onclick="closeStatusModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-8 space-y-6">
        <!-- Appointment Info -->
        <div>
          <h3 class="text-lg font-bold mb-4">Appointment Details</h3>
          <div class="bg-gray-800/50 rounded-lg p-4 space-y-2 text-sm">
            <p><span class="text-gray-400">Client:</span> <span class="font-medium" id="modalClientName"></span></p>
            <p><span class="text-gray-400">Barber:</span> <span class="font-medium" id="modalBarberName"></span></p>
            <p><span class="text-gray-400">Service:</span> <span class="font-medium" id="modalServiceName"></span></p>
            <p><span class="text-gray-400">Date & Time:</span> <span class="font-medium" id="modalDateTime"></span></p>
            <p><span class="text-gray-400">Current Status:</span> <span class="font-medium" id="modalCurrentStatus"></span></p>
          </div>
        </div>

        <!-- Status Selection -->
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-3">
            New Status <span class="text-red-400">*</span>
          </label>
          <div class="space-y-2">
            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
              <input type="radio" name="status" value="pending" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600">
              <span class="text-sm">Pending</span>
            </label>
            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
              <input type="radio" name="status" value="confirmed" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600">
              <span class="text-sm">Confirmed</span>
            </label>
            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
              <input type="radio" name="status" value="completed" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600">
              <span class="text-sm">Completed</span>
            </label>
            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
              <input type="radio" name="status" value="cancelled" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600">
              <span class="text-sm">Cancelled</span>
            </label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
          <button 
            type="button" 
            onclick="closeStatusModal()"
            class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button 
            type="button"
            onclick="updateStatus()"
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
          >
            Update Status
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  <div id="logoutModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-8 max-w-md w-full">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold mb-2">Confirm Logout</h3>
        <p class="text-gray-400">Are you sure you want to log out?</p>
      </div>
      <div class="flex space-x-3">
        <button onclick="closeLogoutModal()" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold transition">
          Cancel
        </button>
        <a href="../auth/logout.php" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 rounded-xl font-semibold transition text-center">
          Logout
        </a>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    let currentAppointmentId = null;

    function confirmLogout() {
      document.getElementById('logoutModal').classList.remove('hidden');
    }

    function closeLogoutModal() {
      document.getElementById('logoutModal').classList.add('hidden');
    }

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
        const logoutModal = document.getElementById('logoutModal');
        const statusModal = document.getElementById('statusModal');
        
        if (logoutModal && !logoutModal.classList.contains('hidden')) {
          closeLogoutModal();
        } else if (statusModal.classList.contains('show')) {
          closeStatusModal();
        } else if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          overlay.classList.remove('show');
        }
      }
    });

    function openStatusModal(appointment) {
      currentAppointmentId = appointment.appointment_id;
      
      document.getElementById('modalClientName').textContent = appointment.customer_first_name + ' ' + appointment.customer_last_name;
      document.getElementById('modalBarberName').textContent = appointment.barber_first_name + ' ' + appointment.barber_last_name;
      document.getElementById('modalServiceName').textContent = appointment.service_name;
      document.getElementById('modalDateTime').textContent = formatDate(appointment.appointment_date) + ' at ' + formatTime(appointment.appointment_time);
      document.getElementById('modalCurrentStatus').textContent = capitalizeStatus(appointment.status);
      
      // Pre-select current status
      document.querySelector(`input[name="status"][value="${appointment.status}"]`).checked = true;
      
      document.getElementById('statusModal').classList.add('show');
    }

    function closeStatusModal() {
      document.getElementById('statusModal').classList.remove('show');
      currentAppointmentId = null;
    }

    function updateStatus() {
      const selectedStatus = document.querySelector('input[name="status"]:checked');
      
      if (!selectedStatus || !currentAppointmentId) {
        alert('Please select a status');
        return;
      }

      const newStatus = selectedStatus.value;

      fetch('../auth/update_appointment_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          appointment_id: currentAppointmentId,
          status: newStatus
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Status updated successfully!');
          closeStatusModal();
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
      });
    }

    function formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function formatTime(timeStr) {
      const [hours, minutes] = timeStr.split(':');
      const hour = parseInt(hours);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const displayHour = hour % 12 || 12;
      return `${displayHour}:${minutes} ${ampm}`;
    }

    function capitalizeStatus(status) {
      return status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Close modal when clicking outside
    document.getElementById('statusModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeStatusModal();
      }
    });
  </script>

</body>
</html>
