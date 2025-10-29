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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Barber | TrimBook Admin</title>
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

    .form-input {
      transition: all 0.3s ease;
    }

    .form-input:focus {
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
    }

    .photo-preview {
      display: none;
    }

    .photo-preview.show {
      display: block;
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

       <a href="admin_addBarber.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
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
  <header class="bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <div class="flex items-center space-x-4">
        <!-- Menu Button for Sidebar -->
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
    <div class="container mx-auto max-w-4xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <div class="flex items-center space-x-4 mb-4">
          <a href="admin_dashboard.php" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </a>
          <h1 class="text-4xl md:text-5xl font-black">
            Add New Barber
          </h1>
        </div>
        <p class="text-gray-400 text-lg">Create a new barber account for your team</p>
      </div>

      <!-- Form Card -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Barber Information</h2>
        </div>

        <!-- Form -->
       <form id="barber-form" action="../auth/process_addnew_barber.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
          
          <!-- Profile Photo Upload -->
          <div>
            <h3 class="text-xl font-bold mb-4 text-purple-400">Profile Photo</h3>
            <div class="flex flex-col items-center space-y-4">
              <!-- Photo Preview -->
              <div id="photoPreview" class="photo-preview w-32 h-32 rounded-full overflow-hidden border-4 border-purple-500">
                <img id="previewImage" src="" alt="Preview" class="w-full h-full object-cover">
              </div>
              
              <!-- Upload Button -->
              <div class="flex flex-col items-center">
                <label for="profile_photo" class="cursor-pointer bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105 flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span>Upload Photo</span>
                </label>
                <input 
                  type="file" 
                  id="profile_photo" 
                  name="profile_photo" 
                  accept="image/jpeg,image/jpg,image/png"
                  class="hidden"
                  onchange="previewPhoto(event)"
                >
                <p class="text-xs text-gray-400 mt-2">JPG, JPEG or PNG (Max 2MB)</p>
              </div>
            </div>
          </div>

          <!-- User Account Information (for users table) -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <!-- First Name -->
              <div>
                <label for="first_name" class="block text-sm font-semibold text-gray-300 mb-2">
                  First Name <span class="text-red-400">*</span>
                </label>
                <input 
                  type="text" 
                  id="first_name" 
                  name="first_name" 
                  required
                  maxlength="50"
                  pattern="[A-Za-z\s]+"
                  title="Only letters and spaces allowed"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="Enter first name"
                >
              </div>

              <!-- Last Name -->
              <div>
                <label for="last_name" class="block text-sm font-semibold text-gray-300 mb-2">
                  Last Name <span class="text-red-400">*</span>
                </label>
                <input 
                  type="text" 
                  id="last_name" 
                  name="last_name" 
                  required
                  maxlength="50"
                  pattern="[A-Za-z\s]+"
                  title="Only letters and spaces allowed"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="Enter last name"
                >
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                  Email Address <span class="text-red-400">*</span>
                </label>
                <input 
                  type="email" 
                  id="email" 
                  name="email" 
                  required
                  maxlength="100"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="barber@example.com"
                >
              </div>

              <!-- Phone -->
              <div>
                <label for="phone_no" class="block text-sm font-semibold text-gray-300 mb-2">
                  Phone Number <span class="text-red-400">*</span>
                </label>
                <input 
                  type="tel" 
                  id="phone_no" 
                  name="phone_no" 
                  required
                  pattern="[0-9]{11}"
                  maxlength="11"
                  title="Please enter exactly 11 digits"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="09123456789"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                >
                <p class="text-xs text-gray-400 mt-1">11 digits only (e.g., 09123456789)</p>
              </div>

              <!-- Username -->
              <div>
                <label for="username" class="block text-sm font-semibold text-gray-300 mb-2">
                  Username <span class="text-red-400">*</span>
                </label>
                <input 
                  type="text" 
                  id="username" 
                  name="username" 
                  required
                  maxlength="50"
                  pattern="[A-Za-z0-9_]+"
                  title="Only letters, numbers and underscores allowed"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="Enter username"
                >
              </div>

              <!-- Password -->
              <div>
                <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">
                  Password <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                  <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    minlength="6"
                    maxlength="255"
                    class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                    placeholder="Enter password"
                  >
                  <button 
                    type="button" 
                    onclick="togglePassword()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
                  >
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Minimum 6 characters</p>
              </div>

            </div>
          </div>

          <!-- Barber-Specific Information (for barbers table) -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Professional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <!-- Specialization -->
              <div>
                <label for="specialization" class="block text-sm font-semibold text-gray-300 mb-2">
                  Specialization
                </label>
                <input 
                  type="text" 
                  id="specialization" 
                  name="specialization"
                  maxlength="100"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="e.g., Haircut, Beard Styling"
                >
              </div>

              <!-- Experience Years -->
              <div>
                <label for="experience_years" class="block text-sm font-semibold text-gray-300 mb-2">
                  Years of Experience
                </label>
                <input 
                  type="number" 
                  id="experience_years" 
                  name="experience_years" 
                  min="0" 
                  max="50"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="e.g., 5"
                >
              </div>

            </div>
          </div>

          <!-- Working Schedule (for schedules table) -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Working Schedule</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              
              <!-- Start Time -->
              <div>
                <label for="start_time" class="block text-sm font-semibold text-gray-300 mb-2">
                  Start Time <span class="text-red-400">*</span>
                </label>
                <input 
                  type="time" 
                  id="start_time" 
                  name="start_time" 
                  required
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white focus:outline-none focus:border-purple-500"
                >
              </div>

              <!-- End Time -->
              <div>
                <label for="end_time" class="block text-sm font-semibold text-gray-300 mb-2">
                  End Time <span class="text-red-400">*</span>
                </label>
                <input 
                  type="time" 
                  id="end_time" 
                  name="end_time" 
                  required
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white focus:outline-none focus:border-purple-500"
                >
              </div>

            </div>

            <!-- Working Days -->
            <div>
              <label class="block text-sm font-semibold text-gray-300 mb-3">
                Working Days <span class="text-red-400">*</span>
              </label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Monday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Monday</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Tuesday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Tuesday</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Wednesday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Wednesday</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Thursday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Thursday</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Friday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Friday</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Saturday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Saturday</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition">
                  <input type="checkbox" name="working_days[]" value="Sunday" class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                  <span class="text-sm">Sunday</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
            <a 
              href="admin_dashboard.php" 
              class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
            >
              Cancel
            </a>
            <button 
              type="submit" 
              class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
            >
              Add Barber
            </button>
          </div>

        </form>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center">
    <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
  </footer>

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

    // Close sidebar when pressing Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const logoutModal = document.getElementById('logoutModal');
        
        if (logoutModal && !logoutModal.classList.contains('hidden')) {
          closeLogoutModal();
        } else if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          overlay.classList.remove('show');
        }
      }
    });

    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
        `;
      } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        `;
      }
    }

    // Photo preview function
    function previewPhoto(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('photoPreview');
      const previewImage = document.getElementById('previewImage');
      
      if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
          alert('Please select a valid image file (JPG, JPEG, or PNG)');
          event.target.value = '';
          return;
        }
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          alert('File size must be less than 2MB');
          event.target.value = '';
          return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.src = e.target.result;
          preview.classList.add('show');
        };
        reader.readAsDataURL(file);
      }
    }

    // Form validation before submit
    document.getElementById('barber-form').addEventListener('submit', function(e) {
      const workingDays = document.querySelectorAll('input[name="working_days[]"]:checked');
      
      if (workingDays.length === 0) {
        e.preventDefault();
        alert('Please select at least one working day.');
        return false;
      }
      
      // Validate phone number
      const phoneInput = document.getElementById('phone_no');
      if (phoneInput.value.length !== 11) {
        e.preventDefault();
        alert('Phone number must be exactly 11 digits.');
        return false;
      }
      
      // If validation passes, the form submits normally
    });
  </script>

</body>
</html>
