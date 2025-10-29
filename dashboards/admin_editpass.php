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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Password | Admin</title>
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
  </style>
</head>
<body class="bg-black text-white antialiased">

  <!-- Overlay -->
  <div id="overlay" class="overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-40" onclick="toggleSidebar()"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-80 bg-gradient-to-b from-gray-900 to-gray-950 border-r border-gray-800 z-50 overflow-y-auto">
    <div class="p-6">
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black tracking-tight">ADMIN MENU</h2>
        <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

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

        <a href="../dashboards/admin_manageservices.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
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
        <a href="../dashboards/admin_editpass.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Change Admin Password</span>
        </a>
      </nav>

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
    <div class="container mx-auto max-w-4xl">
      
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">Change Admin Password</h1>
        <p class="text-gray-400 text-lg">Update your administrator account password</p>
      </div>

      <!-- Success Message -->
      <div id="successMessage" class="hidden mb-6 bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl p-6 border border-green-500/30">
        <div class="flex items-center space-x-3">
          <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <div>
            <h3 class="font-bold text-lg">Password Changed Successfully!</h3>
            <p class="text-sm text-white/80">Your admin password has been updated.</p>
          </div>
        </div>
      </div>

      <!-- Password Form -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Password Settings</h2>
          <p class="text-sm text-white/80 mt-1">Ensure your account is using a secure password</p>
        </div>

        <form id="passwordForm" class="p-8 space-y-6">
          
          <!-- Current Password -->
          <div>
            <label for="currentPassword" class="block text-sm font-semibold text-gray-300 mb-2">
              Current Password
            </label>
            <div class="relative">
              <input 
                type="password" 
                id="currentPassword" 
                required
                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 pr-12"
                placeholder="Enter your current password"
              >
              <button 
                type="button" 
                onclick="togglePassword('currentPassword')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
              >
                👁️
              </button>
            </div>
            <p id="currentError" class="hidden text-red-400 text-sm mt-2">Current password is incorrect</p>
          </div>

          <!-- New Password -->
          <div>
            <label for="newPassword" class="block text-sm font-semibold text-gray-300 mb-2">
              New Password
            </label>
            <div class="relative">
              <input 
                type="password" 
                id="newPassword" 
                required
                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 pr-12"
                placeholder="Enter your new password"
              >
              <button 
                type="button" 
                onclick="togglePassword('newPassword')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
              >
                👁️
              </button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="confirmPassword" class="block text-sm font-semibold text-gray-300 mb-2">
              Confirm New Password
            </label>
            <div class="relative">
              <input 
                type="password" 
                id="confirmPassword" 
                required
                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 pr-12"
                placeholder="Confirm your new password"
              >
              <button 
                type="button" 
                onclick="togglePassword('confirmPassword')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
              >
                👁️
              </button>
            </div>
            <p id="confirmError" class="hidden text-red-400 text-sm mt-2">Passwords do not match</p>
          </div>

          <!-- Buttons -->
          <div class="flex gap-4 pt-4">
            <button 
              type="button" 
              onclick="cancelForm()"
              class="flex-1 px-6 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-gray-300 font-semibold hover:bg-gray-800 transition"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition"
            >
              Update Password
            </button>
          </div>
        </form>
      </div>

    </div>
  </main>

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
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('overlay').classList.toggle('show');
    }

    function confirmLogout() {
      document.getElementById('logoutModal').classList.remove('hidden');
    }

    function closeLogoutModal() {
      document.getElementById('logoutModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const logoutModal = document.getElementById('logoutModal');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        if (logoutModal && !logoutModal.classList.contains('hidden')) {
          closeLogoutModal();
        } else if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          overlay.classList.remove('show');
        }
      }
    });

    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      field.type = field.type === 'password' ? 'text' : 'password';
    }

    function checkPasswordMatch() {
      const confirmError = document.getElementById('confirmError');
      const newPass = document.getElementById('newPassword').value;
      const confirmPass = document.getElementById('confirmPassword').value;
      
      if (confirmPass && newPass !== confirmPass) {
        confirmError.classList.remove('hidden');
      } else {
        confirmError.classList.add('hidden');
      }
    }

    document.getElementById('confirmPassword').addEventListener('input', checkPasswordMatch);

    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      
      // Check if passwords match
      if (newPassword !== confirmPassword) {
        document.getElementById('confirmError').classList.remove('hidden');
        return;
      }
      
      try {
        const response = await fetch('../auth/update_admin_password.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          document.getElementById('successMessage').classList.remove('hidden');
          document.getElementById('passwordForm').reset();
          
          setTimeout(() => {
            document.getElementById('successMessage').classList.add('hidden');
          }, 5000);
        } else {
          if (data.error === 'incorrect_current_password') {
            document.getElementById('currentError').classList.remove('hidden');
          }
          alert(data.message || 'Failed to update password');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Failed to update password. Please try again.');
      }
    });

    function cancelForm() {
      if (confirm('Are you sure you want to cancel? Any changes will be lost.')) {
        document.getElementById('passwordForm').reset();
        window.location.href = 'admin_dashboard.php';
      }
    }
  </script>

</body>
</html>
