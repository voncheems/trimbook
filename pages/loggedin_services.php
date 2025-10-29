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
        <li><a href="/trimbook/pages/loggedin_services.php" class="text-white transition font-semibold">Services</a></li>
        <li><a href="/trimbook/pages/loggedin_ourbarbers.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
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
      <div class="mb-10 text-center">
        <h1 class="text-4xl md:text-5xl font-black mb-2">Our Services</h1>
        <p class="text-gray-400 text-lg">Premium grooming services tailored for you</p>
      </div>

      <!-- Services Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
        
        <!-- Service 1 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="../assets/images/classic.jpeg" alt="Classic Haircut" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Classic Haircut</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Clean, Sharp and Timeless.</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              35-45 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold">₱250.00</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 2 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="../assets/images/fade.jpeg" alt="Beard Trim & Shape" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Fade & Line up</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Sharp fades with clean edges.</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              35-45 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold">₱300.00</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 3 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="../assets/images/kids.jpeg" alt="Premium Hair & Beard Combo" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Kid's Haircut</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Gentle and stylish cuts for kids.</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              50-60 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold">₱200.00</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

      <!-- Service 4 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="../assets/images/towel.jpeg" alt="Premium Hair & Beard Combo" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Shave & Towel</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Smooth shave with relaxing hot towel.</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              25-35 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold">₱300.00</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>
        
      <!-- Service 5 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="../assets/images/modern.jpeg" alt="Premium Hair & Beard Combo" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Modern & Trendy</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Styled/Fresh cuts for the latest look.</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              40-50 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold">₱300.00</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

      <!-- Service 6 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="../assets/images/beard.jpeg" alt="Premium Hair & Beard Combo" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Beard Grooming</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Beard shaping, trimming & styling.</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              20-30 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold">₱250.00</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

      </div>

      

      <!-- CTA Section (Full-Width and Centered) -->
      <div class="mt-24 w-full bg-gradient-to-r from-blue-600/20 to-purple-600/20 border border-blue-500/30 rounded-3xl py-16 px-8 flex flex-col md:flex-row items-center justify-between gap-10 max-w-7xl mx-auto">
        <div class="max-w-xl">
          <h2 class="text-5xl font-black mb-3 leading-tight">Ready to Book?</h2>
          <p class="text-gray-300 text-lg">Choose your preferred barber and time slot to get started</p>
        </div>
        <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-14 py-5 rounded-full font-bold text-lg hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
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
            <li><a href="/trimbook/pages/loggedin_services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
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

</body>
</html>
