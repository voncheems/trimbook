<?php
session_start();

// Debug: Check session
error_log("=== BARBER DASHBOARD DEBUG ===");
error_log("Session ID: " . session_id());
error_log("Session data: " . json_encode($_SESSION));

// Check what we have in session
if (!isset($_SESSION['user_id'])) {
    error_log("ERROR: No user_id in session");
    die("DEBUG: No user_id. Session: " . json_encode($_SESSION));
}

if (!isset($_SESSION['user_type'])) {
    error_log("ERROR: No user_type in session");
    die("DEBUG: No user_type. Session: " . json_encode($_SESSION));
}

error_log("user_type value: '" . $_SESSION['user_type'] . "'");
error_log("user_type length: " . strlen($_SESSION['user_type']));

// Check if it's barber (case-sensitive exact match)
if ($_SESSION['user_type'] !== 'barber') {
    error_log("ERROR: user_type is not 'barber', it is: '" . $_SESSION['user_type'] . "'");
    die("DEBUG: user_type mismatch. Got: '" . $_SESSION['user_type'] . "' Expected: 'barber'");
}

error_log("User authenticated as barber successfully");

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

// Fetch barber's appointments and profile photo from database
$appointments = [];
$profile_photo = null;
$db_error = null;

try {
    error_log("Attempting database connection");
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        $db_error = "Connection failed: " . $conn->connect_error;
        throw new Exception($db_error);
    }
    
    error_log("Database connected successfully");
    
    // Get profile_photo from users table
    error_log("Getting profile_photo for user_id: " . $barber_user_id);
    $user_query = "SELECT profile_photo FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_row = $result->fetch_assoc();
        $profile_photo = $user_row['profile_photo'];
        error_log("Profile photo: " . ($profile_photo ? $profile_photo : "NULL"));
    }
    $stmt->close();
    
    // Get barber_id from barbers table
    error_log("Getting barber_id for user_id: " . $barber_user_id);
    $barber_query = "SELECT barber_id FROM barbers WHERE user_id = ?";
    $stmt = $conn->prepare($barber_query);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $barber_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("Query executed, rows: " . $result->num_rows);
    
    if ($result->num_rows > 0) {
        $barber_row = $result->fetch_assoc();
        $barber_id = $barber_row['barber_id'];
        error_log("Found barber_id: " . $barber_id);
        
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
            error_log("Appointments query prepare failed: " . $conn->error);
            throw new Exception("Appointments query prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $barber_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        
        error_log("Fetched " . count($appointments) . " appointments");
    } else {
        error_log("No barber found for user_id: " . $barber_user_id);
        $db_error = "Barber profile not found for this user";
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Exception in barber dashboard: " . $e->getMessage());
    $db_error = "Error: " . $e->getMessage();
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
  <title>Barber Dashboard | TrimBook</title>
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
    
    .card-hover {
      transition: all 0.3s ease;
    }
    
    .card-hover:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
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
      <!-- Sidebar Header -->
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black tracking-tight">MENU</h2>
        <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Profile Section -->
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

      <!-- Navigation Menu -->
      <nav class="space-y-2">
        <a href="../dashboards/barber_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>

        <a href="../dashboards/barber_appointments.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span>My Appointments</span>
        </a>

        <a href="../dashboards/barber_profile.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <span>My Profile</span>
        </a>

        <a href="../dashboards/barber_schedpage.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>Schedule</span>
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
        <a href="#" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-green-500 text-sm">BARBER</span></a>
      </div>

      <div class="flex items-center space-x-6">
        <span class="text-gray-400 text-sm hidden md:block">Welcome, <span class="text-white font-semibold"><?= htmlspecialchars($first_name) ?></span></span>
        <a href="../auth/logout.php" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</a>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-6xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-4">
          Your Dashboard
        </h1>
        <p class="text-gray-400 text-lg">View and manage your appointments</p>
      </div>

      <!-- Database Error Alert -->
      <?php if ($db_error): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($db_error) ?></p>
        </div>
      <?php endif; ?>

      <!-- Appointments Card -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Your Scheduled Appointments</h2>
        </div>

        <!-- Table / Empty State -->
        <?php if (count($appointments) > 0): ?>
          <!-- Appointments Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-700">
                <tr class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                  <th class="px-8 py-5 text-left">Client</th>
                  <th class="px-8 py-5 text-left">Phone</th>
                  <th class="px-8 py-5 text-left">Date & Time</th>
                  <th class="px-8 py-5 text-left">Service</th>
                  <th class="px-8 py-5 text-left">Price</th>
                  <th class="px-8 py-5 text-left">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($appointments as $apt): ?>
                  <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-8 py-6 font-medium"><?= htmlspecialchars($apt['customer_first_name'] . ' ' . $apt['customer_last_name']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= htmlspecialchars($apt['customer_phone'] ?? 'N/A') ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= formatDateTime($apt['appointment_date'], $apt['appointment_time']) ?></td>
                    <td class="px-8 py-6 text-gray-300"><?= htmlspecialchars($apt['service_name']) ?></td>
                    <td class="px-8 py-6 text-green-400 font-semibold">₱<?= number_format($apt['price'], 2) ?></td>
                    <td class="px-8 py-6">
                      <?php $badge = getStatusBadge($apt['status']); ?>
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $badge['bg'] ?> <?= $badge['text'] ?> border <?= $badge['border'] ?>">
                        <?= $badge['label'] ?>
                      </span>
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
            <p class="text-gray-400 text-lg font-medium">No appointments scheduled</p>
            <p class="text-gray-500 text-sm mt-2">Your scheduled appointments will appear here</p>
          </div>
        <?php endif; ?>
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
  </script>

</body>
</html>
