<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'barber') {
    header("Location: ../auth/login.php");
    exit();
}

$barber_user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'Barber';
$last_name = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? 'barber';
$email = $_SESSION['email'] ?? '';
$full_name = trim($first_name . ' ' . $last_name);

$initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
if (empty(trim($initials))) {
    $initials = strtoupper(substr($username, 0, 2));
}

// Fetch barber profile data from database
$profile_data = [];
$barber_data = [];
$error = '';
$success = '';
$profile_photo = null;

try {
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Fetch user data
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $profile_data = $result->fetch_assoc();
        $profile_photo = $profile_data['profile_photo'];
    }
    $stmt->close();
    
    // Fetch barber-specific data
    $barber_query = "SELECT * FROM barbers WHERE user_id = ?";
    $stmt = $conn->prepare($barber_query);
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $barber_data = $result->fetch_assoc();
    }
    $stmt->close();
    
} catch (Exception $e) {
    $error = "Error loading profile: " . $e->getMessage();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $barber_user_id) {
    $first_name_new = $_POST['first_name'] ?? '';
    $last_name_new = $_POST['last_name'] ?? '';
    $email_new = $_POST['email'] ?? '';
    $phone_new = $_POST['phone'] ?? '';
    $specialization_new = $_POST['specialization'] ?? '';
    
    if (!empty($first_name_new) && !empty($last_name_new) && !empty($email_new)) {
        try {
            // Update users table
            $update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_no = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssssi", $first_name_new, $last_name_new, $email_new, $phone_new, $barber_user_id);
            
            if ($update_stmt->execute()) {
                // Update barbers table if exists
                if (!empty($barber_data)) {
                    $barber_update = "UPDATE barbers SET specialization = ? WHERE user_id = ?";
                    $barber_stmt = $conn->prepare($barber_update);
                    $barber_stmt->bind_param("si", $specialization_new, $barber_user_id);
                    $barber_stmt->execute();
                    $barber_stmt->close();
                }
                
                $_SESSION['first_name'] = $first_name_new;
                $_SESSION['last_name'] = $last_name_new;
                $_SESSION['email'] = $email_new;
                $first_name = $first_name_new;
                $last_name = $last_name_new;
                $full_name = trim($first_name . ' ' . $last_name);
                $email = $email_new;
                $success = "Profile updated successfully!";
                
                // Refresh profile data
                $query = "SELECT * FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $barber_user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $profile_data = $result->fetch_assoc();
                }
                $stmt->close();
                
                // Refresh barber data
                $barber_query = "SELECT * FROM barbers WHERE user_id = ?";
                $stmt = $conn->prepare($barber_query);
                $stmt->bind_param("i", $barber_user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $barber_data = $result->fetch_assoc();
                }
                $stmt->close();
            } else {
                $error = "Error updating profile. Please try again.";
            }
            $update_stmt->close();
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

if (isset($conn)) {
    $conn->close();
}

$member_since = isset($profile_data['created_at']) ? date('F Y', strtotime($profile_data['created_at'])) : 'Unknown';
$phone = $profile_data['phone_no'] ?? '';
$specialization = $barber_data['specialization'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barber Profile | TrimBook</title>
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
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold overflow-hidden">
            <?php if (!empty($profile_photo)): ?>
              <img src="../<?= htmlspecialchars($profile_photo) ?>" alt="Profile" class="w-full h-full object-cover" onerror="this.style.display='none'; this.parentElement.innerHTML='<?= htmlspecialchars($initials) ?>';">
            <?php else: ?>
              <?= htmlspecialchars($initials) ?>
            <?php endif; ?>
          </div>
          <div>
            <h3 class="font-bold text-lg"><?= htmlspecialchars($full_name) ?></h3>
            <p class="text-sm text-white/80">@<?= htmlspecialchars($username) ?></p>
          </div>
        </div>
      </div>

      <nav class="space-y-2">
        <a href="/trimbook/dashboards/barber_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>

        <a href="/trimbook/dashboards/barber_appointments.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span>My Appointments</span>
        </a>

        <a href="/trimbook/dashboards/barber_profile.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <span>My Profile</span>
        </a>

        <a href="/trimbook/dashboards/barber_schedpage.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>Schedule</span>
        </a>
      </nav>

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
        <a href="/trimbook/pages/homepage_loggedin.php" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-green-500 text-sm">BARBER</span></a>
      </div>
      <div class="flex items-center space-x-6">
        <span class="text-gray-400 text-sm hidden md:block">Welcome, <span class="text-white font-semibold"><?= htmlspecialchars($first_name) ?></span></span>
        <a href="../auth/logout.php" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</a>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-5xl">
      
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">My Profile</h1>
        <p class="text-gray-400 text-lg">Manage your barber account information</p>
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
              <div class="w-40 h-40 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl border-4 border-gray-800 flex items-center justify-center text-6xl font-bold overflow-hidden">
                <?php if (!empty($profile_photo)): ?>
                  <img src="../<?= htmlspecialchars($profile_photo) ?>" alt="Profile" class="w-full h-full object-cover" onerror="this.style.display='none'; this.parentElement.innerHTML='<?= htmlspecialchars($initials) ?>';">
                <?php else: ?>
                  <?= htmlspecialchars($initials) ?>
                <?php endif; ?>
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
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Username</label>
              <div class="text-white text-lg font-medium"><?= htmlspecialchars($username) ?></div>
              <p class="text-gray-500 text-sm mt-2">Username cannot be changed</p>
            </div>

            <!-- Specialization -->
            <div>
              <label class="text-gray-400 text-sm font-medium block mb-3">Specialization</label>
              <div id="displaySpecialization" class="text-white text-lg font-medium"><?= !empty($specialization) ? htmlspecialchars($specialization) : '<span class="text-gray-500">Not provided</span>' ?></div>
              <input type="text" name="specialization" id="editSpecialization" value="<?= htmlspecialchars($specialization) ?>" placeholder="e.g., Fades, Beard Styling" class="hidden w-full edit-input px-4 py-3 rounded-lg text-white text-lg" />
            </div>
          </div>
        </div>
      </form>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center">
    <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
  </footer>

  <script>
    let isEditing = false;

    function toggleEdit() {
      isEditing = !isEditing;
      
      const displayFields = ['displayFirstName', 'displayLastName', 'displayEmail', 'displayPhone', 'displaySpecialization'];
      const editFields = ['editFirstName', 'editLastName', 'editEmail', 'editPhone', 'editSpecialization'];
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
        const sidebar = document.getElementById('sidebar');
        if (sidebar.classList.contains('open')) {
          sidebar.classList.remove('open');
          document.getElementById('overlay').classList.remove('show');
        }
      }
    });
  </script>

</body>
</html>
