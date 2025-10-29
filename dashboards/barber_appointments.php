<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'barber') {
    header("Location: ../pages/login_page.php");
    exit();
}

// Check if user has correct role
if ($_SESSION['user_type'] !== 'barber') {
    header('Location: ../unauthorized.php');
    exit;
}

// Include database connection
require_once '../includes/dbconfig.php';

$barber_user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'Barber';
$last_name = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? 'barber';
$full_name = trim($first_name . ' ' . $last_name);

// Get initials for avatar
$initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
if (empty(trim($initials))) {
    $initials = strtoupper(substr($username, 0, 2));
}

// Fetch appointments from database
$appointments = [];
$profile_photo = null;
$db_error = null;

try {
    // Get profile_photo from users table
    $user_query = "SELECT profile_photo FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_row = $result->fetch_assoc();
        $profile_photo = $user_row['profile_photo'];
    }
    $stmt->close();
    
    // Get barber_id from barbers table
    $barber_query = "SELECT barber_id FROM barbers WHERE user_id = ?";
    $stmt = $conn->prepare($barber_query);
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $barber_row = $result->fetch_assoc();
        $barber_id = $barber_row['barber_id'];
        
        // Fetch appointments for this barber
        $query = "
            SELECT 
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                u.first_name as customer_first_name,
                u.last_name as customer_last_name,
                u.phone_no as customer_phone,
                s.service_name,
                s.price,
                a.created_at
            FROM appointments a
            JOIN users u ON a.customer_user_id = u.user_id
            JOIN services s ON a.service_id = s.service_id
            WHERE a.barber_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $db_error = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("i", $barber_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            
            $stmt->close();
        }
    } else {
        $db_error = "Barber profile not found";
    }
} catch (Exception $e) {
    $db_error = "Error fetching appointments";
}

function getStatusBadge($status) {
    $badges = [
        'pending' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500/30', 'label' => 'In Progress'],
        'confirmed' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-400', 'border' => 'border-green-500/30', 'label' => 'Confirmed'],
        'completed' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'border-blue-500/30', 'label' => 'Completed'],
        'cancelled' => ['bg' => 'bg-red-500/20', 'text' => 'text-red-400', 'border' => 'border-red-500/30', 'label' => 'Cancelled']
    ];
    return $badges[$status] ?? $badges['pending'];
}

function formatDate($date) {
    $dateObj = new DateTime($date);
    return $dateObj->format('M d, Y');
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

$filter = $_GET['filter'] ?? 'not-completed';
$valid_filters = ['not-completed', 'all', 'completed', 'cancelled'];
if (!in_array($filter, $valid_filters)) {
    $filter = 'not-completed';
}

$filtered_appointments = array_filter($appointments, function($apt) use ($filter) {
    if ($filter === 'not-completed') {
        return in_array($apt['status'], ['pending', 'confirmed']);
    } elseif ($filter === 'completed') {
        return $apt['status'] === 'completed';
    } elseif ($filter === 'cancelled') {
        return $apt['status'] === 'cancelled';
    }
    return true;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Appointments | TrimBook</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3); }
    .sidebar { transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
    .sidebar.open { transform: translateX(0); }
    .overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease-in-out; }
    .overlay.show { opacity: 1; pointer-events: auto; }
  </style>
</head>
<body class="bg-black text-white antialiased">

  <div id="overlay" class="overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-40" onclick="toggleSidebar()"></div>

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
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          <span>Dashboard</span>
        </a>
        
        <a href="/trimbook/dashboards/barber_appointments.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          <span>My Appointments</span>
        </a>

        <a href="/trimbook/dashboards/barber_profile.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
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
        <button onclick="confirmLogout()" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          <span>Logout</span>
        </button>
      </div>
    </div>
  </aside>

  <header class="bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <div class="flex items-center space-x-4">
        <button onclick="toggleSidebar()" class="text-white hover:text-purple-400 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <a href="/trimbook/dashboards/barber_dashboard.php" class="text-2xl font-black">TRIMBOOK <span class="text-green-500 text-sm">BARBER</span></a>
      </div>
      <div class="flex items-center space-x-6">
        <span class="text-gray-400 text-sm hidden md:block">Welcome, <span class="text-white font-semibold"><?= htmlspecialchars($first_name) ?></span></span>
        <button onclick="confirmLogout()" class="text-sm text-gray-300 hover:text-white hidden md:block">Logout</button>
      </div>
    </nav>
  </header>

  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-6xl">
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">My Appointments</h1>
        <p class="text-gray-400 text-lg">View and manage your client appointments</p>
      </div>

      <div class="mb-8 bg-gray-900/50 rounded-2xl p-2 inline-flex gap-2 border border-gray-800 flex-wrap">
        <a href="?filter=not-completed" class="px-6 py-3 rounded-xl text-sm font-semibold transition <?= $filter === 'not-completed' ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' ?>">Not Completed</a>
        <a href="?filter=all" class="px-6 py-3 rounded-xl text-sm font-semibold transition <?= $filter === 'all' ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' ?>">All</a>
        <a href="?filter=completed" class="px-6 py-3 rounded-xl text-sm font-semibold transition <?= $filter === 'completed' ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' ?>">Completed</a>
        <a href="?filter=cancelled" class="px-6 py-3 rounded-xl text-sm font-semibold transition <?= $filter === 'cancelled' ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' ?>">Cancelled</a>
      </div>

      <?php if ($db_error): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($db_error) ?></p>
        </div>
      <?php endif; ?>

      <div id="alertMessage" class="hidden mb-6 px-6 py-4 rounded-xl"></div>

      <div class="space-y-4">
        <?php if (count($filtered_appointments) === 0): ?>
          <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl px-8 py-16 text-center">
            <h3 class="text-xl font-semibold text-white mb-2">No appointments found</h3>
          </div>
        <?php else: ?>
          <?php foreach ($filtered_appointments as $apt): ?>
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-6 card-hover" id="appointment-<?= $apt['appointment_id'] ?>">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($apt['service_name']) ?></h3>
                  <p class="text-sm text-gray-300"><?= htmlspecialchars($apt['customer_first_name'] . ' ' . $apt['customer_last_name']) ?></p>
                  <?php if (!empty($apt['customer_phone'])): ?>
                    <p class="text-xs text-gray-400 mt-1">📞 <?= htmlspecialchars($apt['customer_phone']) ?></p>
                  <?php endif; ?>
                </div>
                <?php $badge = getStatusBadge($apt['status']); ?>
                <span class="px-4 py-2 rounded-full text-sm font-semibold <?= $badge['bg'] ?> <?= $badge['text'] ?> border <?= $badge['border'] ?>"><?= $badge['label'] ?></span>
              </div>

              <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                <div class="text-gray-300">
                  <p class="text-xs text-gray-500 mb-1">Date</p>
                  <p class="font-semibold"><?= formatDate($apt['appointment_date']) ?></p>
                </div>
                <div class="text-gray-300">
                  <p class="text-xs text-gray-500 mb-1">Time</p>
                  <p class="font-semibold"><?= formatTime($apt['appointment_time']) ?></p>
                </div>
                <div class="text-gray-300">
                  <p class="text-xs text-gray-500 mb-1">Price</p>
                  <p class="font-semibold text-green-400">₱<?= number_format($apt['price'], 2) ?></p>
                </div>
              </div>

              <div class="flex gap-3 pt-4 border-t border-gray-700">
                <?php if ($apt['status'] === 'pending'): ?>
                  <button onclick="markComplete(<?= $apt['appointment_id'] ?>)" class="px-5 py-2.5 text-sm font-semibold text-blue-400 bg-blue-500/10 rounded-xl hover:bg-blue-500/20 border border-blue-500/30 transition">Mark Complete</button>
                  <button onclick="updateStatus(<?= $apt['appointment_id'] ?>, 'cancelled')" class="px-5 py-2.5 text-sm font-semibold text-red-400 bg-red-500/10 rounded-xl hover:bg-red-500/20 border border-red-500/30 transition">Cancel</button>
                <?php elseif ($apt['status'] === 'confirmed'): ?>
                  <button onclick="startService(<?= $apt['appointment_id'] ?>)" class="px-5 py-2.5 text-sm font-semibold text-yellow-400 bg-yellow-500/10 rounded-xl hover:bg-yellow-500/20 border border-yellow-500/30 transition">Start Service</button>
                  <button onclick="updateStatus(<?= $apt['appointment_id'] ?>, 'cancelled')" class="px-5 py-2.5 text-sm font-semibold text-red-400 bg-red-500/10 rounded-xl hover:bg-red-500/20 border border-red-500/30 transition">Cancel</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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

  <script>
    function confirmLogout() {
      document.getElementById('logoutModal').classList.remove('hidden');
    }

    function closeLogoutModal() {
      document.getElementById('logoutModal').classList.add('hidden');
    }

    // Start service - changes confirmed to pending (in progress) without confirmation
    function startService(appointmentId) {
      fetch('../auth/update_appointment_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `appointment_id=${appointmentId}&status=pending`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('Service started - customer is now in progress', 'success');
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert(data.message || 'Error updating appointment', 'error');
        }
      })
      .catch(error => showAlert('An error occurred', 'error'));
    }

    // Mark complete - changes pending to completed without confirmation
    function markComplete(appointmentId) {
      fetch('../auth/update_appointment_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `appointment_id=${appointmentId}&status=completed`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('Service completed successfully', 'success');
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert(data.message || 'Error updating appointment', 'error');
        }
      })
      .catch(error => showAlert('An error occurred', 'error'));
    }

    function updateStatus(appointmentId, status) {
      const statusLabels = {
        'pending': 'mark as pending',
        'confirmed': 'confirm',
        'completed': 'mark as completed',
        'cancelled': 'cancel'
      };
      
      if (confirm(`Are you sure you want to ${statusLabels[status]} this appointment?`)) {
        fetch('../auth/update_appointment_status.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `appointment_id=${appointmentId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showAlert(`Appointment ${statusLabels[status]}ed successfully`, 'success');
            setTimeout(() => location.reload(), 1500);
          } else {
            showAlert(data.message || 'Error updating appointment', 'error');
          }
        })
        .catch(error => showAlert('An error occurred', 'error'));
      }
    }

    function showAlert(message, type) {
      const alertDiv = document.getElementById('alertMessage');
      alertDiv.textContent = message;
      alertDiv.className = 'mb-6 px-6 py-4 rounded-xl ' + (type === 'success' ? 'bg-green-500/20 border border-green-500/30 text-green-400' : 'bg-red-500/20 border border-red-500/30 text-red-400');
      alertDiv.classList.remove('hidden');
      setTimeout(() => alertDiv.classList.add('hidden'), 3000);
    }

    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('overlay').classList.toggle('show');
    }

    // Enhanced Escape key handler
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
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
  </script>
</body>
</html>
