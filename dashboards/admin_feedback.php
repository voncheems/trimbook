<?php
session_start();
require_once('../includes/dbconfig.php');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../pages/login_page.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_username = $_SESSION['admin_username'] ?? 'admin';
$initials = strtoupper(substr($admin_name, 0, 2));

// Fetch all feedback
$feedback_data = [];
$query = "
    SELECT 
        f.feedback_id,
        f.appointment_id,
        f.rating,
        f.comment,
        f.created_at,
        u.first_name as customer_first_name,
        u.last_name as customer_last_name,
        b_user.first_name as barber_first_name,
        b_user.last_name as barber_last_name,
        s.service_name
    FROM feedback f
    JOIN users u ON f.customer_user_id = u.user_id
    JOIN barbers b ON f.barber_id = b.barber_id
    JOIN users b_user ON b.user_id = b_user.user_id
    JOIN appointments a ON f.appointment_id = a.appointment_id
    JOIN services s ON a.service_id = s.service_id
    ORDER BY f.created_at DESC
";

$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $feedback_data[] = $row;
    }
}

$total_feedback = count($feedback_data);

// Calculate average rating
$avg_rating = 0;
if ($total_feedback > 0) {
    $sum = array_sum(array_column($feedback_data, 'rating'));
    $avg_rating = $sum / $total_feedback;
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function getRatingColor($rating) {
    if ($rating >= 4.5) return 'text-green-400';
    if ($rating >= 3) return 'text-yellow-400';
    return 'text-red-400';
}

function getStarDisplay($rating) {
    $filled = '★';
    $empty = '☆';
    $stars = str_repeat($filled, $rating) . str_repeat($empty, 5 - $rating);
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Feedback | TrimBook Admin</title>
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

        <a href="admin_feedback.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-800/50 text-white font-medium hover:bg-gray-800 transition">
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
        <h1 class="text-4xl md:text-5xl font-black mb-2">Customer Feedback</h1>
        <p class="text-gray-400 text-lg">Total: <span class="text-blue-400 font-semibold"><?= $total_feedback ?></span> feedbacks received</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-2">Total Feedbacks</p>
              <h3 class="text-3xl font-bold text-blue-400"><?= $total_feedback ?></h3>
            </div>
            <svg class="w-12 h-12 text-blue-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
          </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-2">Average Rating</p>
              <h3 class="text-3xl font-bold <?= getRatingColor($avg_rating) ?>"><?= number_format($avg_rating, 1) ?> / 5.0</h3>
            </div>
            <svg class="w-12 h-12 text-yellow-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
          </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-2">5-Star Ratings</p>
              <h3 class="text-3xl font-bold text-green-400"><?= count(array_filter($feedback_data, fn($f) => $f['rating'] == 5)) ?></h3>
            </div>
            <svg class="w-12 h-12 text-green-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Feedback Table -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <?php if ($total_feedback > 0): ?>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/50 border-b border-gray-700">
                <tr class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                  <th class="px-6 py-5 text-left">Customer</th>
                  <th class="px-6 py-5 text-left">Barber</th>
                  <th class="px-6 py-5 text-left">Service</th>
                  <th class="px-6 py-5 text-center">Rating</th>
                  <th class="px-6 py-5 text-left">Comment</th>
                  <th class="px-6 py-5 text-left">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                <?php foreach ($feedback_data as $feedback): ?>
                  <tr class="hover:bg-gray-800/50 transition">
                    <td class="px-6 py-5 font-medium text-blue-400">
                      <?= htmlspecialchars($feedback['customer_first_name'] . ' ' . $feedback['customer_last_name']) ?>
                    </td>
                    <td class="px-6 py-5 text-gray-300">
                      <?= htmlspecialchars($feedback['barber_first_name'] . ' ' . $feedback['barber_last_name']) ?>
                    </td>
                    <td class="px-6 py-5 text-gray-300">
                      <?= htmlspecialchars($feedback['service_name']) ?>
                    </td>
                    <td class="px-6 py-5 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <span class="<?= getRatingColor($feedback['rating']) ?> text-lg"><?= getStarDisplay($feedback['rating']) ?></span>
                        <span class="text-gray-400 text-sm"><?= $feedback['rating'] ?>/5</span>
                      </div>
                    </td>
                    <td class="px-6 py-5 text-gray-300 max-w-xs">
                      <?php if (!empty($feedback['comment'])): ?>
                        <button onclick="document.getElementById('commentModal').classList.remove('hidden'); document.getElementById('commentModal').classList.add('flex'); document.getElementById('modalCustomer').textContent = '<?= addslashes($feedback['customer_first_name'] . ' ' . $feedback['customer_last_name']) ?>'; document.getElementById('modalBarber').textContent = '<?= addslashes($feedback['barber_first_name'] . ' ' . $feedback['barber_last_name']) ?>'; document.getElementById('modalComment').textContent = `<?= addslashes($feedback['comment']) ?>`;" class="text-blue-400 hover:text-blue-300 transition">
                          View
                        </button>
                      <?php else: ?>
                        <span class="text-gray-500">-</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-5 text-gray-400 text-sm">
                      <?= formatDate($feedback['created_at']) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            <p class="text-gray-400 text-lg">No feedback yet</p>
            <p class="text-gray-500 text-sm mt-2">Customer feedback will appear here once appointments are completed</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Comment Modal -->
  <div id="commentModal" class="fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl w-full max-w-2xl mx-6">
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
        <h2 class="text-2xl font-bold">Customer Feedback</h2>
      </div>

      <div class="p-8">
        <div class="mb-6">
          <p class="text-gray-400 text-sm mb-2">From</p>
          <p class="text-white font-semibold" id="modalCustomer"></p>
          <p class="text-gray-400 text-sm">for</p>
          <p class="text-white font-semibold" id="modalBarber"></p>
        </div>

        <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-6 max-h-64 overflow-y-auto">
          <p class="text-gray-300 whitespace-pre-wrap break-words" id="modalComment"></p>
        </div>

        <div class="flex justify-end mt-6">
          <button onclick="closeCommentModal()" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showCommentModal(customer, barber, comment) {
      document.getElementById('modalCustomer').textContent = customer;
      document.getElementById('modalBarber').textContent = barber;
      document.getElementById('modalComment').textContent = comment;
      document.getElementById('commentModal').classList.remove('hidden');
      document.getElementById('commentModal').classList.add('flex');
    }

    function closeCommentModal() {
      document.getElementById('commentModal').classList.remove('flex');
      document.getElementById('commentModal').classList.add('hidden');
    }

    document.getElementById('commentModal')?.addEventListener('click', function(e) {
      if (e.target === this) closeCommentModal();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeCommentModal();
    });
  </script>
</body>
</html>
