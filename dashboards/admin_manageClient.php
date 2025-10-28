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

// Fetch all clients from database
$clients = [];
$db_error = null;
    
    $query = "
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            u.phone_no,
            u.created_at,
            COUNT(a.appointment_id) as total_appointments,
            MAX(a.appointment_date) as last_appointment_date
        FROM users u
        LEFT JOIN appointments a ON u.user_id = a.customer_user_id
        WHERE u.user_type = 'customer'
        GROUP BY u.user_id
        ORDER BY u.created_at DESC
    ";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    
// Function to format date
function formatDate($date) {
    if (!$date) return 'Never';
    $dateObj = new DateTime($date);
    return $dateObj->format('M d, Y');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Clients | TrimBook Admin</title>
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

    .client-row:hover {
      background-color: rgba(255, 255, 255, 0.05);
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

        <a href="admin_manageClient.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
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
          <span>Manage Contacts</span>
        </a>

        <a href="../dashboards/admin_feedback.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
          </svg>
          <span>Customer Feedback</span>
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
    <div class="container mx-auto max-w-7xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-2">Manage Clients</h1>
        <p class="text-gray-400 text-lg">Total: <span class="text-blue-400 font-semibold"><?= count($clients) ?></span> clients</p>
      </div>

      <!-- Error Message -->
      <?php if ($db_error): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($db_error) ?></p>
        </div>
      <?php endif; ?>

      <!-- Success Message -->
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
      <?php endif; ?>

      <!-- Error Message from Form -->
      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
      <?php endif; ?>

      <!-- Clients Table -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">All Clients (<?= count($clients) ?>)</h2>
        </div>

        <!-- Table or Empty State -->
        <?php if (count($clients) > 0): ?>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-700">
                <tr class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                  <th class="px-8 py-5 text-left">Name</th>
                  <th class="px-8 py-5 text-left">Email</th>
                  <th class="px-8 py-5 text-left">Phone</th>
                  <th class="px-8 py-5 text-center">Total Appointments</th>
                  <th class="px-8 py-5 text-left">Last Visit</th>
                  <th class="px-8 py-5 text-left">Joined</th>
                  <th class="px-8 py-5 text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clients as $client): ?>
                  <tr class="client-row border-b border-gray-800 transition">
                    <td class="px-8 py-6 font-medium"><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= htmlspecialchars($client['email']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= htmlspecialchars($client['phone_no'] ?? 'N/A') ?></td>
                    <td class="px-8 py-6 text-center font-semibold text-purple-400"><?= $client['total_appointments'] ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= formatDate($client['last_appointment_date']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= formatDate($client['created_at']) ?></td>
                    <td class="px-8 py-6 text-center">
                      <button onclick="openClientModal(<?= htmlspecialchars(json_encode($client)) ?>)" class="text-blue-400 hover:text-blue-300 font-medium transition">
                        View
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <p class="text-gray-400 text-lg font-medium">No clients yet</p>
            <p class="text-gray-500 text-sm mt-2">Clients will appear here once they create accounts</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Client Details Modal -->
  <div id="clientModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-md mx-6 max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 sticky top-0">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold">Client Details</h2>
          <button onclick="closeClientModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-8 space-y-6">
        <!-- Client Info -->
        <div>
          <h3 class="text-lg font-bold mb-4">Personal Information</h3>
          <div class="bg-gray-800/50 rounded-lg p-4 space-y-3 text-sm">
            <p><span class="text-gray-400">Full Name:</span> <span class="font-medium" id="modalClientName"></span></p>
            <p><span class="text-gray-400">Email:</span> <span class="font-medium" id="modalClientEmail"></span></p>
            <p><span class="text-gray-400">Phone:</span> <span class="font-medium" id="modalClientPhone"></span></p>
            <p><span class="text-gray-400">Member Since:</span> <span class="font-medium" id="modalClientJoined"></span></p>
          </div>
        </div>

        <!-- Appointment Stats -->
        <div>
          <h3 class="text-lg font-bold mb-4">Appointment History</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gradient-to-br from-purple-600/20 to-purple-700/10 border border-purple-500/30 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-purple-400" id="modalTotalAppointments">0</p>
              <p class="text-xs text-gray-400 mt-1">Total Appointments</p>
            </div>
            <div class="bg-gradient-to-br from-blue-600/20 to-blue-700/10 border border-blue-500/30 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-blue-400" id="modalLastVisit">Never</p>
              <p class="text-xs text-gray-400 mt-1">Last Visit</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
          <button 
            type="button" 
            onclick="closeClientModal()"
            class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Close
          </button>
          <button 
            type="button"
            onclick="openPasswordModal()"
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
          >
            Reset Password
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Password Reset Modal -->
  <div id="passwordModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-md mx-6">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold">Reset Password</h2>
          <button onclick="closePasswordModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <form id="passwordResetForm" method="POST" action="../auth/reset_client_password.php" class="p-8 space-y-6">
        <input type="hidden" id="resetUserId" name="user_id">
        
        <div>
          <p class="text-gray-400 mb-4">Reset password for: <span class="text-white font-semibold" id="resetClientName"></span></p>
          
          <label for="newPassword" class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
          <input 
            type="password" 
            id="newPassword" 
            name="new_password" 
            required
            minlength="6"
            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white focus:outline-none focus:border-purple-500 transition"
            placeholder="Enter new password (min. 6 characters)"
          >
        </div>

        <div>
          <label for="confirmPassword" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
          <input 
            type="password" 
            id="confirmPassword" 
            name="confirm_password" 
            required
            minlength="6"
            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white focus:outline-none focus:border-purple-500 transition"
            placeholder="Confirm new password"
          >
        </div>

        <div id="passwordError" class="hidden bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm"></div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-4">
          <button 
            type="button" 
            onclick="closePasswordModal()"
            class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button 
            type="submit"
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
          >
            Reset Password
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center no-print">
    <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
  </footer>

  <!-- JavaScript -->
  <script>
    let currentClientId = null;

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeClientModal();
        closePasswordModal();
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          overlay.classList.remove('show');
        }
      }
    });

    function openClientModal(client) {
      currentClientId = client.user_id;
      document.getElementById('modalClientName').textContent = client.first_name + ' ' + client.last_name;
      document.getElementById('modalClientEmail').textContent = client.email;
      document.getElementById('modalClientPhone').textContent = client.phone_no || 'N/A';
      document.getElementById('modalClientJoined').textContent = formatDate(client.created_at);
      document.getElementById('modalTotalAppointments').textContent = client.total_appointments;
      document.getElementById('modalLastVisit').textContent = formatDate(client.last_appointment_date);
      
      document.getElementById('clientModal').classList.add('show');
    }

    function closeClientModal() {
      document.getElementById('clientModal').classList.remove('show');
    }

    function openPasswordModal() {
      const clientName = document.getElementById('modalClientName').textContent;
      document.getElementById('resetUserId').value = currentClientId;
      document.getElementById('resetClientName').textContent = clientName;
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmPassword').value = '';
      document.getElementById('passwordError').classList.add('hidden');
      document.getElementById('passwordModal').classList.add('show');
    }

    function closePasswordModal() {
      document.getElementById('passwordModal').classList.remove('show');
    }

    function formatDate(dateStr) {
      if (!dateStr) return 'Never';
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Close modals when clicking outside
    document.getElementById('clientModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeClientModal();
      }
    });

    document.getElementById('passwordModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closePasswordModal();
      }
    });

    // Password form validation
    document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
      const password = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const errorDiv = document.getElementById('passwordError');

      if (password !== confirmPassword) {
        e.preventDefault();
        errorDiv.textContent = 'Passwords do not match!';
        errorDiv.classList.remove('hidden');
        return false;
      }

      if (password.length < 6) {
        e.preventDefault();
        errorDiv.textContent = 'Password must be at least 6 characters long!';
        errorDiv.classList.remove('hidden');
        return false;
      }

      errorDiv.classList.add('hidden');
      return true;
    });
  </script>

</body>
</html>
