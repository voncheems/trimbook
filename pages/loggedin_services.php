<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: /trimbook/pages/login_page.php');
    exit;
}

// Check if user has correct role for THIS page
if ($_SESSION['user_type'] !== 'customer') {
    header('Location: /trimbook/unauthorized.php');
    exit;
}

// Get user data from session
$first_name = $_SESSION['first_name'] ?? 'Guest';
$last_name = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? 'guest';
$full_name = trim($first_name . ' ' . $last_name);
$initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));

require_once('../includes/dbconfig.php');

// Get services from database
$services = [];
$services_result = $conn->query("SELECT * FROM services");
if ($services_result) {
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
}

$title = "Services | TrimBook";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
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

    .card-hover {
      transition: all 0.3s ease;
    }

    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    }

    .service-image {
      transition: all 0.3s ease;
    }

    .card-hover:hover .service-image {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-black text-white antialiased">



  <!-- Header -->
  <header class="fixed w-full top-0 left-0 z-40 bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <a href="/trimbook/pages/homepage_loggedin.php" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-blue-500 text-sm">CLIENT</span></a>
      <ul class="hidden md:flex space-x-8 font-medium text-sm">
        <li><a href="/trimbook/pages/homepage_loggedin.php" class="text-gray-300 hover:text-white transition">Home</a></li>
        <li><a href="/trimbook/pages/services.php" class="text-white transition font-semibold">Services</a></li>
        <li><a href="/trimbook/pages/ourBarbers_page.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
        <li><a href="/trimbook/dashboards/client_dashboard.php" class="text-gray-300 hover:text-white transition">My Dashboard</a></li>
      </ul>
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-300 hidden lg:block">Hi, <span class="font-semibold text-white"><?= htmlspecialchars($first_name) ?></span></span>
        <a href="/trimbook/dashboards/client_dashboard.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
          My Account
        </a>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6 pt-32">
    <div class="container mx-auto max-w-7xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-black mb-2">Our Services</h1>
        <p class="text-gray-400 text-lg">Premium grooming services tailored for you</p>
      </div>

      <!-- Services Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
        
        <?php if (!empty($services)): ?>
          <?php foreach ($services as $service): ?>
            <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
              <div class="relative h-48 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
                <?php if (!empty($service['image_path'])): ?>
                  <img src="<?= htmlspecialchars($service['image_path']) ?>" alt="<?= htmlspecialchars($service['service_name']) ?>" class="service-image w-full h-full object-cover">
                <?php else: ?>
                  <div class="service-image w-full h-full bg-gradient-to-br from-blue-600/30 to-purple-600/30 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0l9.172 9.172M4 16l4.586-4.586a2 2 0 012.828 0l9.172 9.172m0 0L21 21m-4.586-4.586l4.586 4.586"></path>
                    </svg>
                  </div>
                <?php endif; ?>
              </div>
              <div class="p-8">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="text-2xl font-bold"><?= htmlspecialchars($service['service_name']) ?></h3>
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <p class="text-gray-400 leading-relaxed mb-4"><?= htmlspecialchars($service['description'] ?? 'Premium grooming service') ?></p>
                <div class="flex items-center text-sm text-gray-400 mb-4">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <?= htmlspecialchars($service['duration'] ?? '30-40 minutes') ?>
                </div>
                <div class="flex items-center justify-between">
                  <p class="text-3xl font-bold gradient-text">₱<?= htmlspecialchars(number_format($service['price'], 2)) ?></p>
                  <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Book
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-full p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p class="text-gray-400 text-lg">No services available at the moment</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- CTA Section -->
      <div class="mt-20 bg-gradient-to-r from-blue-600/10 to-purple-600/10 border border-blue-500/20 rounded-3xl p-12 text-center">
        <h2 class="text-3xl md:text-4xl font-black mb-4">Ready to Book?</h2>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-8">Choose your preferred barber and time slot to get started</p>
        <a href="/trimbook/dashboards/client_selectBarber.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-10 py-4 rounded-full font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          Book Your Appointment
        </a>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-12">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8 mb-8">
        <div>
          <h3 class="text-xl font-black mb-4">TRIMBOOK</h3>
          <p class="text-gray-400 text-sm">Your trusted barber appointment system. Book smarter, look sharper.</p>
        </div>
        <div>
          <h4 class="font-bold mb-4">Quick Links</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="/trimbook/pages/homepage_loggedin.php" class="text-gray-400 hover:text-white transition">Home</a></li>
            <li><a href="/trimbook/pages/services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="/trimbook/dashboards/client_selectBarber.php" class="text-gray-400 hover:text-white transition">Book Appointment</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-4">Contact</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li>Email: info@trimbook.com</li>
            <li>Phone: (123) 456-7890</li>
            <li>Address: Baguio City, Philippines</li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-800 pt-8 text-center">
        <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        // No sidebar to close anymore
      }
    });
  </script>

</body>
</html>
