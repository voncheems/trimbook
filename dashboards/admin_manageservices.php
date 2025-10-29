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

// Fetch all services from database
$services = [];
$db_error = null;
    
$query = "SELECT service_id, service_name, description, price FROM services ORDER BY service_name ASC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Services | TrimBook Admin</title>
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

    .modal {
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal.show {
      display: flex;
      opacity: 1;
    }

    .service-row:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }
  </style>
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

        <a href="admin_manageClient.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>Manage Clients</span>
        </a>

        <a href="admin_manageservices.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
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
        <button onclick="confirmLogout()" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</button>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-6xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-4">
            <h1 class="text-4xl md:text-5xl font-black">
              Manage Services
            </h1>
          </div>
          <button onclick="openModal()" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Add Service</span>
          </button>
        </div>
        <p class="text-gray-400 text-lg">Add, edit, and manage barber services</p>
      </div>

      <!-- Error Message -->
      <?php if ($db_error): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($db_error) ?></p>
        </div>
      <?php endif; ?>

      <!-- Services Table -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">All Services (<?= count($services) ?>)</h2>
        </div>

        <!-- Table or Empty State -->
        <?php if (count($services) > 0): ?>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-700">
                <tr class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                  <th class="px-8 py-5 text-left">Service Name</th>
                  <th class="px-8 py-5 text-left">Description</th>
                  <th class="px-8 py-5 text-left">Price</th>
                  <th class="px-8 py-5 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($services as $service): ?>
                  <tr class="service-row border-b border-gray-800 transition">
                    <td class="px-8 py-6 font-semibold"><?= htmlspecialchars($service['service_name']) ?></td>
                    <td class="px-8 py-6 text-gray-300 text-sm max-w-xs truncate">
                      <?= htmlspecialchars($service['description'] ?? 'No description') ?>
                    </td>
                    <td class="px-8 py-6 font-bold text-blue-400">₱<?= number_format($service['price'], 2) ?></td>
                    <td class="px-8 py-6 text-center">
                      <div class="flex items-center justify-center space-x-3">
                        <button onclick='editService(<?= json_encode($service) ?>)' class="text-blue-400 hover:text-blue-300 transition">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button onclick="deleteService(<?= $service['service_id'] ?>, '<?= htmlspecialchars(addslashes($service['service_name'])) ?>')" class="text-red-400 hover:text-red-300 transition">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <p class="text-gray-400 text-lg font-medium">No services yet</p>
            <p class="text-gray-500 text-sm mt-2">Create your first service to get started</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center no-print">
    <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
  </footer>

  <!-- Add/Edit Service Modal -->
  <div id="serviceModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-md mx-6 max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 sticky top-0">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold" id="modalTitle">Add New Service</h2>
          <button onclick="closeModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Form -->
      <form id="serviceForm" class="p-8 space-y-6">
        <input type="hidden" id="serviceId" name="service_id" value="">

        <!-- Service Name -->
        <div>
          <label for="serviceName" class="block text-sm font-semibold text-gray-300 mb-2">
            Service Name <span class="text-red-400">*</span>
          </label>
          <input 
            type="text" 
            id="serviceName" 
            name="service_name" 
            required
            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition"
            placeholder="e.g., Classic Haircut"
          >
        </div>

        <!-- Description -->
        <div>
          <label for="serviceDescription" class="block text-sm font-semibold text-gray-300 mb-2">
            Description
          </label>
          <textarea 
            id="serviceDescription" 
            name="description" 
            rows="3"
            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition resize-none"
            placeholder="Describe this service..."
          ></textarea>
        </div>

        <!-- Price -->
        <div>
          <label for="servicePrice" class="block text-sm font-semibold text-gray-300 mb-2">
            Price (₱) <span class="text-red-400">*</span>
          </label>
          <input 
            type="number" 
            id="servicePrice" 
            name="price" 
            required
            step="0.01"
            min="0"
            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition"
            placeholder="0.00"
          >
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
          <button 
            type="button" 
            onclick="closeModal()"
            class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
            id="submitBtn"
          >
            Add Service
          </button>
        </div>
      </form>
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
    // Logout Modal Functions
    function confirmLogout() {
      document.getElementById('logoutModal').classList.remove('hidden');
    }

    function closeLogoutModal() {
      document.getElementById('logoutModal').classList.add('hidden');
    }

    // Sidebar Functions
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    }

    // Escape Key Handler
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const logoutModal = document.getElementById('logoutModal');
        const serviceModal = document.getElementById('serviceModal');
        
        if (logoutModal && !logoutModal.classList.contains('hidden')) {
          closeLogoutModal();
        } else if (serviceModal.classList.contains('show')) {
          closeModal();
        } else if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          overlay.classList.remove('show');
        }
      }
    });

    // Service Modal Functions
    function openModal() {
      document.getElementById('serviceId').value = '';
      document.getElementById('serviceName').value = '';
      document.getElementById('serviceDescription').value = '';
      document.getElementById('servicePrice').value = '';
      document.getElementById('modalTitle').textContent = 'Add New Service';
      document.getElementById('submitBtn').textContent = 'Add Service';
      document.getElementById('serviceModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('serviceModal').classList.remove('show');
    }

    function editService(service) {
      document.getElementById('serviceId').value = service.service_id;
      document.getElementById('serviceName').value = service.service_name;
      document.getElementById('serviceDescription').value = service.description || '';
      document.getElementById('servicePrice').value = service.price;
      document.getElementById('modalTitle').textContent = 'Edit Service';
      document.getElementById('submitBtn').textContent = 'Update Service';
      document.getElementById('serviceModal').classList.add('show');
    }

    function deleteService(serviceId, serviceName) {
      if (confirm(`Are you sure you want to delete "${serviceName}"?\n\nThis action cannot be undone.`)) {
        fetch('../auth/delete_service.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            service_id: serviceId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(err => {
          console.error('Error:', err);
          alert('An error occurred while deleting the service');
        });
      }
    }

    // Close modal when clicking outside
    document.getElementById('serviceModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });

    // Form Submit Handler
    document.getElementById('serviceForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const submitBtn = document.getElementById('submitBtn');
      const originalText = submitBtn.textContent;
      
      // Disable button and show loading state
      submitBtn.disabled = true;
      submitBtn.textContent = 'Saving...';
      
      const formData = new FormData(this);
      
      fetch('../auth/save_service.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          location.reload();
        } else {
          alert('Error: ' + data.message);
          // Re-enable button
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      })
      .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while saving the service');
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
    });
  </script>
</body>
</html>
