<?php
session_start();
require_once '../includes/dbconfig.php';

$first_name = $_SESSION['first_name'] ?? 'Guest';
$last_name = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? 'guest';
$user_id = $_SESSION['user_id'] ?? null;
$full_name = trim($first_name . ' ' . $last_name);
$email = $_SESSION['email'] ?? '';

$initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
if (empty(trim($initials))) {
    $initials = strtoupper(substr($username, 0, 2));
}

// Fetch user profile data from database
$profile_data = [];
$error = '';
$success = '';
$password_error = '';
$password_success = '';

if ($user_id) {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $profile_data = $result->fetch_assoc();
    }
    $stmt->close();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && $user_id) {
    $first_name_new = $_POST['first_name'] ?? '';
    $last_name_new = $_POST['last_name'] ?? '';
    $email_new = $_POST['email'] ?? '';
    $phone_new = $_POST['phone'] ?? '';
    
    if (!empty($first_name_new) && !empty($last_name_new) && !empty($email_new)) {
        $update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_no = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $first_name_new, $last_name_new, $email_new, $phone_new, $user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['first_name'] = $first_name_new;
            $_SESSION['last_name'] = $last_name_new;
            $first_name = $first_name_new;
            $last_name = $last_name_new;
            $full_name = trim($first_name . ' ' . $last_name);
            $email = $email_new;
            $success = "Profile updated successfully!";
            
            // Refresh profile data
            $query = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $profile_data = $result->fetch_assoc();
            }
            $stmt->close();
        } else {
            $error = "Error updating profile. Please try again.";
        }
        $update_stmt->close();
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password']) && $user_id) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        // Verify current password
        $query = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            
            if (password_verify($current_password, $user_data['password'])) {
                if ($new_password === $confirm_password) {
                    if (strlen($new_password) >= 8) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->bind_param("si", $hashed_password, $user_id);
                        
                        if ($update_stmt->execute()) {
                            $password_success = "Password changed successfully!";
                        } else {
                            $password_error = "Error changing password. Please try again.";
                        }
                        $update_stmt->close();
                    } else {
                        $password_error = "New password must be at least 8 characters long.";
                    }
                } else {
                    $password_error = "New passwords do not match.";
                }
            } else {
                $password_error = "Current password is incorrect.";
            }
        }
        $stmt->close();
    } else {
        $password_error = "Please fill in all password fields.";
    }
}

$member_since = isset($profile_data['created_at']) ? date('F Y', strtotime($profile_data['created_at'])) : 'Unknown';
$phone = $profile_data['phone_no'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Profile | TrimBook</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    body {
      font-family: 'Inter', sans-serif;
    }

    .edit-input {
      background: rgba(102, 126, 234, 0.05);
      border: 1px solid rgba(102, 126, 234, 0.2);
      transition: all 0.3s ease;
    }

    .edit-input:focus {
      background: rgba(102, 126, 234, 0.1);
      border-color: rgba(102, 126, 234, 0.4);
      outline: none;
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
        <h2 class="text-2xl font-black tracking-tight">MENU</h2>
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
            <h3 class="font-bold text-lg"><?= htmlspecialchars($full_name) ?></h3>
            <p class="text-sm text-white/80">@<?= htmlspecialchars($username) ?></p>
          </div>
        </div>
      </div>

      <nav class="space-y-2">
        <a href="/trimbook/dashboards/client_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>

        <a href="/trimbook/dashboards/client_appointments.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span>My Appointments</span>
        </a>

        <a href="/trimbook/dashboards/client_profile.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <span>My Profile</span>
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
        <a href="/trimbook/pages/homepage_loggedin.php" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-blue-500 text-sm">CLIENT</span></a>
      </div>
      <div class="flex items-center space-x-6">
        <span class="text-gray-400 text-sm hidden md:block">Welcome, <span class="text-white font-semibold"><?= htmlspecialchars($first_name) ?></span></span>
        <button onclick="confirmLogout()" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</button>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-5xl">
      
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">My Profile</h1>
        <p class="text-gray-400 text-lg">Manage your account information</p>
      </div>

      <!-- Messages -->
      <?php if (!empty($success)): ?>
        <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-xl">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <!-- Profile Card -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-40"></div>
        <div class="px-8 pb-8">
          <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-20 mb-8">
            <div class="flex items-end space-x-6">
              <div class="w-40 h-40 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl border-4 border-gray-800 flex items-center justify-center text-6xl font-bold">
                <?= htmlspecialchars($initials) ?>
              </div>
              <div>
                <h2 class="text-4xl font-black mb-2"><?= htmlspecialchars($full_name) ?></h2>
                <p class="text-gray-400 text-lg">Member since <?= htmlspecialchars($member_since) ?></p>
              </div>
            </div>
            <button onclick="toggleEdit()" id="editBtn" class="mt-4 md:mt-0 px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold transition">
              Edit Profile
            </button>
            <div id="actionButtons" class="hidden flex space-x-3 mt-4 md:mt-0">
              <button onclick="document.getElementById('profileForm').submit()" class="px-8 py-3 bg-green-600 hover:bg-green-700 rounded-xl font-semibold transition">
                Save Changes
              </button>
              <button onclick="toggleEdit()" class="px-8 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold transition">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Information -->
      <form id="profileForm" method="POST" class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden mb-8">
        <input type="hidden" name="update_profile" value="1">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Personal Information</h2>
        </div>
        <div class="p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- First Name -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">First Name</label>
              <div id="displayFirstName" class="text-white text-lg font-medium"><?= htmlspecialchars($profile_data['first_name'] ?? $first_name) ?></div>
              <input type="text" name="first_name" id="editFirstName" value="<?= htmlspecialchars($profile_data['first_name'] ?? $first_name) ?>" class="hidden w-full edit-input px-4 py-3 rounded-lg text-white text-lg" required />
            </div>

            <!-- Last Name -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Last Name</label>
              <div id="displayLastName" class="text-white text-lg font-medium"><?= htmlspecialchars($profile_data['last_name'] ?? $last_name) ?></div>
              <input type="text" name="last_name" id="editLastName" value="<?= htmlspecialchars($profile_data['last_name'] ?? $last_name) ?>" class="hidden w-full edit-input px-4 py-3 rounded-lg text-white text-lg" required />
            </div>

            <!-- Email -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Email Address</label>
              <div id="displayEmail" class="text-white text-lg font-medium"><?= htmlspecialchars($profile_data['email'] ?? '') ?></div>
              <input type="email" name="email" id="editEmail" value="<?= htmlspecialchars($profile_data['email'] ?? '') ?>" class="hidden w-full edit-input px-4 py-3 rounded-lg text-white text-lg" required />
            </div>

            <!-- Phone -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Phone Number</label>
              <div id="displayPhone" class="text-white text-lg font-medium"><?= !empty($profile_data['phone_no']) ? htmlspecialchars($profile_data['phone_no']) : '<span class="text-gray-500">Not provided</span>' ?></div>
              <input type="tel" name="phone" id="editPhone" value="<?= htmlspecialchars($profile_data['phone_no'] ?? '') ?>" class="hidden w-full edit-input px-4 py-3 rounded-lg text-white text-lg" />
            </div>

            <!-- Username -->
            <div class="md:col-span-2">
              <label class="text-gray-400 text-sm font-medium block mb-3">Username</label>
              <div class="text-white text-lg font-medium"><?= htmlspecialchars($username) ?></div>
              <p class="text-gray-500 text-sm mt-2">Username cannot be changed</p>
            </div>
          </div>
        </div>
      </form>

      <!-- Change Password Section -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Change Password</h2>
        </div>
        <div class="p-8">
          <!-- Password Messages -->
          <?php if (!empty($password_success)): ?>
            <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-xl">
              <?= htmlspecialchars($password_success) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($password_error)): ?>
            <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
              <?= htmlspecialchars($password_error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" class="space-y-6">
            <input type="hidden" name="change_password" value="1">
            
            <!-- Current Password -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Current Password</label>
              <input type="password" name="current_password" class="w-full edit-input px-4 py-3 rounded-lg text-white text-lg" required />
            </div>

            <!-- New Password -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">New Password</label>
              <input type="password" name="new_password" class="w-full edit-input px-4 py-3 rounded-lg text-white text-lg" required minlength="8" />
              <p class="text-gray-500 text-sm mt-2">Password must be at least 8 characters long</p>
            </div>

            <!-- Confirm New Password -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Confirm New Password</label>
              <input type="password" name="confirm_password" class="w-full edit-input px-4 py-3 rounded-lg text-white text-lg" required minlength="8" />
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold transition">
                Change Password
              </button>
            </div>
          </form>
        </div>
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

  <script>
    let isEditing = false;

    function confirmLogout() {
      document.getElementById('logoutModal').classList.remove('hidden');
    }

    function closeLogoutModal() {
      document.getElementById('logoutModal').classList.add('hidden');
    }

    function toggleEdit() {
      isEditing = !isEditing;
      
      const displayFields = ['displayFirstName', 'displayLastName', 'displayEmail', 'displayPhone'];
      const editFields = ['editFirstName', 'editLastName', 'editEmail', 'editPhone'];
      const editBtn = document.getElementById('editBtn');
      const actionButtons = document.getElementById('actionButtons');

      if (isEditing) {
        displayFields.forEach(id => document.getElementById(id).classList.add('hidden'));
        editFields.forEach(id => document.getElementById(id).classList.remove('hidden'));
        editBtn.classList.add('hidden');
        actionButtons.classList.remove('hidden');
      } else {
        displayFields.forEach(id => document.getElementById(id).classList.remove('hidden'));
        editFields.forEach(id => document.getElementById(id).classList.add('hidden'));
        editBtn.classList.remove('hidden');
        actionButtons.classList.add('hidden');
      }
    }

    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('overlay').classList.toggle('show');
    }

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const logoutModal = document.getElementById('logoutModal');
        const sidebar = document.getElementById('sidebar');
        
        if (logoutModal && !logoutModal.classList.contains('hidden')) {
          closeLogoutModal();
        } else if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          document.getElementById('overlay').classList.remove('show');
        }
      }
    });
  </script>

</body>
</html>
