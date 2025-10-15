<?php
session_start();

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

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span>All Appointments</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>Manage Barbers</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>Manage Clients</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span>Manage Services</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Reports & Analytics</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Settings</span>
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
        <!-- Hamburger Menu Button -->
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
        <form action="process_add_barber.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
          
          <!-- Personal Information Section -->
          <div>
            <h3 class="text-xl font-bold mb-4 text-purple-400">Personal Information</h3>
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
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="barber@example.com"
                >
              </div>

              <!-- Phone -->
              <div>
                <label for="phone" class="block text-sm font-semibold text-gray-300 mb-2">
                  Phone Number <span class="text-red-400">*</span>
                </label>
                <input 
                  type="tel" 
                  id="phone" 
                  name="phone" 
                  required
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="+63 912 345 6789"
                >
              </div>

            </div>
          </div>

          <!-- Account Information Section -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
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
              </div>

            </div>

            <!-- Auto-generate Password Option -->
            <div class="mt-4">
              <label class="flex items-center space-x-3 cursor-pointer">
                <input 
                  type="checkbox" 
                  id="auto_generate"
                  onchange="toggleAutoGenerate()"
                  class="w-5 h-5 bg-gray-800 border-gray-600 rounded text-purple-600 focus:ring-purple-500 focus:ring-offset-0"
                >
                <span class="text-sm text-gray-300">Auto-generate secure password</span>
              </label>
            </div>
          </div>

          <!-- Professional Information Section -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Professional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <!-- Specialty -->
              <div>
                <label for="specialty" class="block text-sm font-semibold text-gray-300 mb-2">
                  Specialty <span class="text-red-400">*</span>
                </label>
                <select 
                  id="specialty" 
                  name="specialty" 
                  required
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white focus:outline-none focus:border-purple-500"
                >
                  <option value="">Select specialty</option>
                  <option value="Haircut">Haircut Specialist</option>
                  <option value="Shave">Shaving Expert</option>
                  <option value="Beard">Beard Styling</option>
                  <option value="Color">Hair Coloring</option>
                  <option value="All">All Services</option>
                </select>
              </div>

              <!-- Experience -->
              <div>
                <label for="experience" class="block text-sm font-semibold text-gray-300 mb-2">
                  Years of Experience <span class="text-red-400">*</span>
                </label>
                <input 
                  type="number" 
                  id="experience" 
                  name="experience" 
                  min="0" 
                  max="50"
                  required
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="e.g., 5"
                >
              </div>

              <!-- Hourly Rate -->
              <div>
                <label for="hourly_rate" class="block text-sm font-semibold text-gray-300 mb-2">
                  Hourly Rate (PHP) <span class="text-red-400">*</span>
                </label>
                <input 
                  type="number" 
                  id="hourly_rate" 
                  name="hourly_rate" 
                  min="0" 
                  step="0.01"
                  required
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
                  placeholder="e.g., 500.00"
                >
              </div>

              <!-- Profile Photo -->
              <div>
                <label for="profile_photo" class="block text-sm font-semibold text-gray-300 mb-2">
                  Profile Photo
                </label>
                <input 
                  type="file" 
                  id="profile_photo" 
                  name="profile_photo" 
                  accept="image/*"
                  class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white file:font-semibold hover:file:bg-purple-700 file:cursor-pointer focus:outline-none focus:border-purple-500"
                >
              </div>

            </div>

            <!-- Bio -->
            <div class="mt-6">
              <label for="bio" class="block text-sm font-semibold text-gray-300 mb-2">
                Bio/Description
              </label>
              <textarea 
                id="bio" 
                name="bio" 
                rows="4"
                class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 resize-none"
                placeholder="Tell us about this barber's skills and experience..."
              ></textarea>
            </div>
          </div>

          <!-- Working Schedule Section -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Working Schedule</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
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
            <div class="mt-6">
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

          <!-- Status Section -->
          <div class="pt-6 border-t border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-purple-400">Account Status</h3>
            <div class="flex items-center space-x-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-300">Active (Barber can receive bookings)</span>
              </label>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
            <a 
              href="/trimbook/dashboards/admin_dashboard.php" 
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

  <!-- JavaScript -->
  <script>
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
        if (sidebar.classList.contains('open')) {
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

    // Auto-generate password
    function toggleAutoGenerate() {
      const checkbox = document.getElementById('auto_generate');
      const passwordInput = document.getElementById('password');
      
      if (checkbox.checked) {
        const generatedPassword = generatePassword();
        passwordInput.value = generatedPassword;
        passwordInput.readOnly = true;
        passwordInput.classList.add('bg-gray-700/50');
      } else {
        passwordInput.value = '';
        passwordInput.readOnly = false;
        passwordInput.classList.remove('bg-gray-700/50');
      }
    }

    // Generate secure password
    function generatePassword() {
      const length = 12;
      const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
      let password = "";
      for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
      }
      return password;
    }

    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
      const workingDays = document.querySelectorAll('input[name="working_days[]"]:checked');
      
      if (workingDays.length === 0) {
        e.preventDefault();
        alert('Please select at least one working day.');
        return false;
      }
    });
  </script>

</body>
</html>
