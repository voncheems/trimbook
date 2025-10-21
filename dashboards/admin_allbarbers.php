<?php
session_start();

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

// Include database configuration
require_once '../includes/dbconfig.php';

// Initialize variables
$barbers = [];
$total_barbers = 0;

// Fetch all barbers with their user information
if (isset($conn) && $conn) {
    $query = "
        SELECT 
            b.barber_id,
            b.specialization,
            b.experience_years,
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            u.phone_no,
            u.username,
            u.profile_photo,
            u.created_at,
            COUNT(DISTINCT a.appointment_id) as total_appointments
        FROM barbers b
        JOIN users u ON b.user_id = u.user_id
        LEFT JOIN appointments a ON b.barber_id = a.barber_id
        WHERE u.user_type = 'barber'
        GROUP BY b.barber_id, b.specialization, b.experience_years, u.user_id, 
                 u.first_name, u.last_name, u.email, u.phone_no, u.username, u.profile_photo, u.created_at
        ORDER BY u.first_name ASC, u.last_name ASC
    ";
    
    $result = $conn->query($query);
    
    if ($result) {
        $total_barbers = $result->num_rows;
        while ($row = $result->fetch_assoc()) {
            $barbers[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Barbers | TrimBook Admin</title>
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

    .modal {
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal.show {
      display: flex;
      opacity: 1;
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

    .file-input-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
    }

    .file-input-wrapper input[type="file"] {
      position: absolute;
      left: -9999px;
    }

    .preview-image {
      max-width: 200px;
      max-height: 200px;
      object-fit: cover;
      margin-top: 10px;
      border-radius: 8px;
    }

    .schedule-checkbox {
      accent-color: #9333ea;
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

  <!-- Header -->
  <header class="bg-black/80 backdrop-blur-lg border-b border-gray-800 no-print">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <div class="flex items-center space-x-4">
        <button onclick="toggleSidebar()" class="text-white hover:text-purple-400 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <a href="/trimbook/dashboards/admin_dashboard.php" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-purple-500 text-sm">ADMIN</span></a>
      </div>
      <div class="flex items-center space-x-6">
        <span class="text-gray-400 text-sm hidden md:block">Welcome, <span class="text-white font-semibold"><?= htmlspecialchars($admin_name) ?></span></span>
        <a href="../auth/logout.php" class="text-sm font-medium text-gray-300 hover:text-white transition hidden md:block">Logout</a>
      </div>
    </nav>
  </header>

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
            <?= htmlspecialchars(strtoupper(substr($admin_name, 0, 2))) ?>
          </div>
          <div>
            <h3 class="font-bold text-lg"><?= htmlspecialchars($admin_name) ?></h3>
            <p class="text-sm text-white/80">@admin</p>
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

        <a href="../dashboards/admin_allAppointment.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
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

        <a href="../dashboards/admin_allbarbers.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>Manage Barbers</span>
        </a>

        <a href="../dashboards/admin_manageClient.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
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

        <a href="../dashboards/admin_reportpage.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Reports & Analytics</span>
        </a>

        <a href="../dashboards/admin_addcontact.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-gray-800/50 hover:text-white transition">
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

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-7xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-2">Manage Barbers</h1>
        <p class="text-gray-400 text-lg">Total: <span class="text-blue-400 font-semibold"><?= $total_barbers ?></span> barbers</p>
      </div>

      <!-- Barbers Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (count($barbers) > 0): ?>
          <?php foreach ($barbers as $barber): 
            $initials = strtoupper(substr($barber['first_name'], 0, 1) . substr($barber['last_name'], 0, 1));
            $joined_date = (new DateTime($barber['created_at']))->format('M d, Y');
          ?>
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden hover:border-purple-500/50 transition">
              <!-- Card Header -->
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-center relative group">
                <?php if (!empty($barber['profile_photo'])): ?>
                  <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white/20 mx-auto mb-3 relative">
                    <img src="../<?= htmlspecialchars($barber['profile_photo']) ?>" alt="<?= htmlspecialchars($barber['first_name']) ?>" class="w-full h-full object-cover">
                    <button onclick='openPhotoModal(<?= htmlspecialchars(json_encode($barber)) ?>)' class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </button>
                  </div>
                <?php else: ?>
                  <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-3 relative group">
                    <?= htmlspecialchars($initials) ?>
                    <button onclick='openPhotoModal(<?= htmlspecialchars(json_encode($barber)) ?>)' class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                      </svg>
                    </button>
                  </div>
                <?php endif; ?>
                <h3 class="text-2xl font-bold"><?= htmlspecialchars($barber['first_name'] . ' ' . $barber['last_name']) ?></h3>
                <p class="text-sm text-white/80 mt-1">@<?= htmlspecialchars($barber['username']) ?></p>
              </div>

              <!-- Card Body -->
              <div class="p-6 space-y-4">
                <!-- Specialization -->
                <div>
                  <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Specialization</p>
                  <p class="text-white font-medium"><?= htmlspecialchars($barber['specialization'] ?? 'General') ?></p>
                </div>

                <!-- Experience -->
                <div>
                  <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Experience</p>
                  <p class="text-white font-medium"><?= htmlspecialchars($barber['experience_years'] ?? 0) ?> years</p>
                </div>

                <!-- Contact Info -->
                <div>
                  <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Contact</p>
                  <p class="text-sm text-gray-300"><?= htmlspecialchars($barber['email']) ?></p>
                  <p class="text-sm text-gray-300"><?= htmlspecialchars($barber['phone_no'] ?? 'N/A') ?></p>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-700">
                  <div class="text-center">
                    <p class="text-2xl font-bold text-blue-400"><?= htmlspecialchars($barber['total_appointments']) ?></p>
                    <p class="text-xs text-gray-400">Appointments</p>
                  </div>
                  <div class="text-center">
                    <p class="text-sm font-medium text-gray-300"><?= htmlspecialchars($joined_date) ?></p>
                    <p class="text-xs text-gray-400">Joined</p>
                  </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-3 gap-2 pt-4">
                  <button onclick='viewBarberDetails(<?= htmlspecialchars(json_encode($barber)) ?>)' class="bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition text-sm">
                    View
                  </button>
                  <button onclick='editBarber(<?= htmlspecialchars(json_encode($barber)) ?>)' class="bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl transition text-sm" title="Edit">
                    Edit
                  </button>
                  <button onclick='confirmDeleteBarber(<?= htmlspecialchars(json_encode($barber)) ?>)' class="bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl transition text-sm" title="Delete">
                    Delete
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Empty State -->
          <div class="col-span-full">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-12 text-center">
              <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <p class="text-gray-400 text-lg">No barbers found</p>
              <p class="text-gray-500 text-sm mt-2">Add barbers to get started</p>
            </div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- View Details Modal -->
  <div id="detailsModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-2xl mx-6 max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 sticky top-0">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold" id="detailsModalTitle">Barber Details</h2>
          <button onclick="closeDetailsModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-8" id="detailsContent">
        <!-- Content will be populated by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Edit Barber Modal -->
  <div id="editModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-2xl mx-6 max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 sticky top-0">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold">Edit Barber</h2>
          <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Form -->
      <form id="editBarberForm" class="p-8 space-y-6">
        <input type="hidden" id="editBarberId" name="barber_id" value="">
        <input type="hidden" id="editUserId" name="user_id" value="">

        <!-- Phone Number -->
        <div>
          <label for="editPhoneNo" class="block text-sm font-semibold text-gray-300 mb-2">
            Phone Number
          </label>
          <input 
            type="text" 
            id="editPhoneNo" 
            name="phone_no" 
            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition"
            placeholder="e.g., 09123456789"
          >
        </div>

        <!-- Specialization -->
        <div>
          <label for="editSpecialization" class="block text-sm font-semibold text-gray-300 mb-2">
            Specialization
          </label>
          <input 
            type="text" 
            id="editSpecialization" 
            name="specialization" 
            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition"
            placeholder="e.g., Hair Styling, Beard Trimming"
          >
        </div>

        <!-- Experience Years -->
        <div>
          <label for="editExperience" class="block text-sm font-semibold text-gray-300 mb-2">
            Experience (years)
          </label>
          <input 
            type="number" 
            id="editExperience" 
            name="experience_years" 
            min="0"
            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition"
            placeholder="0"
          >
        </div>

        <!-- Schedule Section -->
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-4">
            Working Schedule
          </label>
          <div class="bg-gray-800/30 border border-gray-700 rounded-xl p-6 space-y-4">
            
            <!-- Days of Week -->
            <div class="grid grid-cols-2 gap-4">
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedMonday" name="schedule_days[]" value="Monday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedMonday" class="text-white cursor-pointer">Monday</label>
              </div>
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedTuesday" name="schedule_days[]" value="Tuesday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedTuesday" class="text-white cursor-pointer">Tuesday</label>
              </div>
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedWednesday" name="schedule_days[]" value="Wednesday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedWednesday" class="text-white cursor-pointer">Wednesday</label>
              </div>
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedThursday" name="schedule_days[]" value="Thursday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedThursday" class="text-white cursor-pointer">Thursday</label>
              </div>
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedFriday" name="schedule_days[]" value="Friday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedFriday" class="text-white cursor-pointer">Friday</label>
              </div>
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedSaturday" name="schedule_days[]" value="Saturday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedSaturday" class="text-white cursor-pointer">Saturday</label>
              </div>
              <div class="flex items-center space-x-3">
                <input type="checkbox" id="schedSunday" name="schedule_days[]" value="Sunday" class="schedule-checkbox w-5 h-5 rounded">
                <label for="schedSunday" class="text-white cursor-pointer">Sunday</label>
              </div>
            </div>

            <!-- Time Inputs -->
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-700">
              <div>
                <label for="editStartTime" class="block text-xs text-gray-400 uppercase tracking-wider mb-2">
                  Start Time
                </label>
                <input 
                  type="time" 
                  id="editStartTime" 
                  name="start_time" 
                  class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-purple-500 transition"
                >
              </div>
              <div>
                <label for="editEndTime" class="block text-xs text-gray-400 uppercase tracking-wider mb-2">
                  End Time
                </label>
                <input 
                  type="time" 
                  id="editEndTime" 
                  name="end_time" 
                  class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-purple-500 transition"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
          <button 
            type="button" 
            onclick="closeEditModal()"
            class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
          >
            Update Barber
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Photo Upload Modal -->
  <div id="photoModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-md mx-6 max-h-[90vh] overflow-y-auto">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 sticky top-0">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold">Update Photo</h2>
          <button onclick="closePhotoModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Form -->
      <form id="photoUploadForm" class="p-8 space-y-6">
        <input type="hidden" id="photoUserId" name="user_id" value="">
        
        <!-- File Input -->
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-4">
            Select Profile Photo
          </label>
          <div class="relative">
            <label class="w-full px-6 py-4 bg-gray-800/50 border-2 border-dashed border-gray-600 rounded-xl text-white hover:border-purple-500 transition cursor-pointer block">
              <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Click to upload or drag and drop
              <input type="file" id="photoFileInput" name="profile_photo" accept="image/*" class="hidden" onchange="previewPhoto(event)">
            </label>
          </div>
          <p class="text-xs text-gray-400 mt-2">PNG, JPG, GIF up to 5MB</p>
        </div>

        <!-- Preview -->
        <div id="photoPreviewContainer" class="hidden">
          <img id="photoPreview" class="preview-image mx-auto" alt="Preview">
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
          <button 
            type="button" 
            onclick="closePhotoModal()"
            class="px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
          >
            Upload Photo
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-red-700 rounded-3xl w-full max-w-md mx-6">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold">Confirm Delete</h2>
          <button onclick="closeDeleteModal()" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-8">
        <div class="flex items-center justify-center mb-6">
          <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
        </div>
        
        <h3 class="text-xl font-bold text-center mb-2">Delete Barber?</h3>
        <p class="text-gray-400 text-center mb-6">
          Are you sure you want to delete <span id="deleteBarberName" class="text-white font-semibold"></span>? This action cannot be undone.
        </p>

        <input type="hidden" id="deleteBarberId" value="">
        <input type="hidden" id="deleteUserId" value="">

        <!-- Action Buttons -->
        <div class="flex items-center space-x-4">
          <button 
            onclick="closeDeleteModal()"
            class="flex-1 px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button 
            onclick="deleteBarber()"
            class="flex-1 px-8 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeDetailsModal();
        closeEditModal();
        closePhotoModal();
        closeDeleteModal();
      }
    });

    function viewBarberDetails(barber) {
      let avatarHTML;
      if (barber.profile_photo) {
        avatarHTML = `<img src="../${barber.profile_photo}" alt="${barber.first_name}" class="w-20 h-20 rounded-full object-cover border-4 border-purple-500">`;
      } else {
        avatarHTML = `<div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-2xl font-bold">${barber.first_name.charAt(0)}${barber.last_name.charAt(0)}</div>`;
      }
      
      const content = `
        <div class="space-y-6">
          <div class="flex items-center space-x-4 pb-6 border-b border-gray-700">
            ${avatarHTML}
            <div>
              <h3 class="text-2xl font-bold">${barber.first_name} ${barber.last_name}</h3>
              <p class="text-gray-400">@${barber.username}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Email</p>
              <p class="text-white">${barber.email}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Phone</p>
              <p class="text-white">${barber.phone_no || 'N/A'}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Specialization</p>
              <p class="text-white">${barber.specialization || 'General'}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Experience</p>
              <p class="text-white">${barber.experience_years || 0} years</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Appointments</p>
              <p class="text-white font-bold text-2xl text-blue-400">${barber.total_appointments}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Joined Date</p>
              <p class="text-white">${new Date(barber.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
            </div>
          </div>
        </div>
      `;
      
      document.getElementById('detailsContent').innerHTML = content;
      document.getElementById('detailsModal').classList.add('show');
    }

    function closeDetailsModal() {
      document.getElementById('detailsModal').classList.remove('show');
    }

    async function editBarber(barber) {
      document.getElementById('editBarberId').value = barber.barber_id;
      document.getElementById('editUserId').value = barber.user_id;
      document.getElementById('editPhoneNo').value = barber.phone_no || '';
      document.getElementById('editSpecialization').value = barber.specialization || '';
      document.getElementById('editExperience').value = barber.experience_years || 0;

      // Uncheck all schedule checkboxes first
      const checkboxes = document.querySelectorAll('input[name="schedule_days[]"]');
      checkboxes.forEach(cb => cb.checked = false);

      // Reset time inputs
      document.getElementById('editStartTime').value = '';
      document.getElementById('editEndTime').value = '';

      // Fetch barber's schedule
      try {
        const response = await fetch(`../auth/get_barber_schedule.php?barber_id=${barber.barber_id}`);
        const data = await response.json();
        
        if (data.success && data.schedules.length > 0) {
          // Check the appropriate days
          data.schedules.forEach(schedule => {
            const checkbox = document.getElementById('sched' + schedule.day_of_week);
            if (checkbox) {
              checkbox.checked = true;
            }
          });

          // Set times from first schedule (assuming all have same times)
          document.getElementById('editStartTime').value = data.schedules[0].start_time;
          document.getElementById('editEndTime').value = data.schedules[0].end_time;
        }
      } catch (error) {
        console.error('Error fetching schedule:', error);
      }

      document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.remove('show');
    }

    function openPhotoModal(barber) {
      document.getElementById('photoUserId').value = barber.user_id;
      document.getElementById('photoFileInput').value = '';
      document.getElementById('photoPreviewContainer').classList.add('hidden');
      document.getElementById('photoModal').classList.add('show');
    }

    function closePhotoModal() {
      document.getElementById('photoModal').classList.remove('show');
    }

    function confirmDeleteBarber(barber) {
      document.getElementById('deleteBarberId').value = barber.barber_id;
      document.getElementById('deleteUserId').value = barber.user_id;
      document.getElementById('deleteBarberName').textContent = `${barber.first_name} ${barber.last_name}`;
      document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.remove('show');
    }

    function deleteBarber() {
      const barberId = document.getElementById('deleteBarberId').value;
      const userId = document.getElementById('deleteUserId').value;

      fetch('../auth/delete_barber.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          barber_id: barberId,
          user_id: userId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Barber deleted successfully');
          closeDeleteModal();
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while deleting');
      });
    }

    function previewPhoto(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('photoPreview').src = e.target.result;
          document.getElementById('photoPreviewContainer').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      }
    }

    // Close modals when clicking outside
    document.getElementById('detailsModal').addEventListener('click', function(e) {
      if (e.target === this) closeDetailsModal();
    });

    document.getElementById('editModal').addEventListener('click', function(e) {
      if (e.target === this) closeEditModal();
    });

    document.getElementById('photoModal').addEventListener('click', function(e) {
      if (e.target === this) closePhotoModal();
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
      if (e.target === this) closeDeleteModal();
    });

    document.getElementById('editBarberForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      // Get selected days
      const selectedDays = [];
      const checkboxes = document.querySelectorAll('input[name="schedule_days[]"]:checked');
      checkboxes.forEach(cb => selectedDays.push(cb.value));

      const data = {
        barber_id: formData.get('barber_id'),
        user_id: formData.get('user_id'),
        phone_no: formData.get('phone_no'),
        specialization: formData.get('specialization'),
        experience_years: formData.get('experience_years'),
        schedule_days: selectedDays,
        start_time: formData.get('start_time'),
        end_time: formData.get('end_time')
      };

      fetch('../auth/update_barber.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Barber updated successfully');
          closeEditModal();
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error('Error:', err);
        alert('An error occurred');
      });
    });

    document.getElementById('photoUploadForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const fileInput = document.getElementById('photoFileInput');
      const userId = document.getElementById('photoUserId').value;

      if (!fileInput.files[0]) {
        alert('Please select a photo');
        return;
      }

      const formData = new FormData();
      formData.append('user_id', userId);
      formData.append('profile_photo', fileInput.files[0]);

      fetch('../auth/upload_barber_photo.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Photo uploaded successfully');
          closePhotoModal();
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while uploading');
      });
    });
  </script>

</body>
</html>
