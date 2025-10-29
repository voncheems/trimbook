<?php
// At the top of EVERY protected page
session_start();
require_once('../includes/dbconfig.php');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: /trimbook/pages/login_page.php');
    exit;
}

// Check if user has correct role for THIS page
if ($_SESSION['user_type'] !== 'customer') {
    header('Location: /unauthorized.php');
    exit;
}

// Get user data from session
$first_name = $_SESSION['first_name'] ?? 'Guest';
$last_name = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? 'guest';
$full_name = trim($first_name . ' ' . $last_name);

// Get count of expert barbers
$barber_result = $conn->query("SELECT COUNT(*) as barber_count FROM barbers");
$barber_row = $barber_result->fetch_assoc();
$expert_barbers = $barber_row['barber_count'] ?? 0;

// Get user's total appointments
$user_id = $_SESSION['user_id'];
$total_appointments = 0;
$appointments_result = $conn->query("
    SELECT COUNT(*) as total_appointments 
    FROM appointments 
    WHERE customer_user_id = {$user_id}
");
if ($appointments_result) {
    $appointments_row = $appointments_result->fetch_assoc();
    $total_appointments = $appointments_row['total_appointments'] ?? 0;
}

// Get user's upcoming appointments
$upcoming_appointments = 0;
$upcoming_result = $conn->query("
    SELECT COUNT(*) as upcoming 
    FROM appointments 
    WHERE customer_user_id = {$user_id} 
    AND appointment_date >= CURDATE()
    AND status IN ('pending', 'confirmed')
");
if ($upcoming_result) {
    $upcoming_row = $upcoming_result->fetch_assoc();
    $upcoming_appointments = $upcoming_row['upcoming'] ?? 0;
}

// Get average rating from feedback
$rating_result = $conn->query("
    SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total_reviews
    FROM feedback
");
$rating_row = $rating_result->fetch_assoc();
$avg_rating = $rating_row['avg_rating'] ?? 0;
$total_reviews = $rating_row['total_reviews'] ?? 0;

$title = "TrimBook | Welcome Back";
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
    
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-gradient {
      background: radial-gradient(ellipse at top, #1a1a2e 0%, #0a0a0f 100%);
      position: relative;
    }
    
    .hero-gradient::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 50% 50%, rgba(102, 126, 234, 0.1) 0%, transparent 50%);
      pointer-events: none;
    }
    
    .card-hover {
      transition: all 0.3s ease;
    }
    
    .card-hover:hover .service-image {
      transform: scale(1.05);
    }

    .service-image {
      transition: all 0.3s ease;
    }

    .stat-card {
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .stat-card:hover::before {
      opacity: 1;
    }

    .mobile-menu {
      display: none;
    }

    .mobile-menu.active {
      display: block;
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

  <!-- Navbar -->
  <header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <a href="/trimbook/pages/homepage_loggedin.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
      
      <!-- Desktop Menu -->
      <ul class="hidden md:flex space-x-8 font-medium text-sm">
        <li><a href="/trimbook/pages/homepage_loggedin.php" class="text-white transition">Home</a></li>
        <li><a href="/trimbook/pages/loggedin_services.php" class="text-gray-300 hover:text-white transition">Services</a></li>
        <li><a href="/trimbook/pages/loggedin_ourbarbers.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
        <li><a href="/trimbook/dashboards/client_dashboard.php" class="text-gray-300 hover:text-white transition">My Dashboard</a></li>
      </ul>
      
      <div class="hidden md:flex items-center space-x-4">
        <span class="text-sm text-gray-300">Hi, <span class="font-semibold text-white"><?= htmlspecialchars($first_name) ?></span></span>
        <a href="/trimbook/dashboards/client_dashboard.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
          My Account
        </a>
      </div>

      <!-- Mobile Menu Button -->
      <button class="md:hidden text-white" onclick="toggleMobileMenu()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu md:hidden bg-black/95 backdrop-blur-lg border-t border-gray-800">
      <ul class="flex flex-col space-y-4 p-6">
        <li><a href="/trimbook/pages/homepage_loggedin.php" class="text-white block">Home</a></li>
        <li><a href="/trimbook/pages/loggedin_services.php" class="text-gray-300 hover:text-white block">Services</a></li>
        <li><a href="/trimbook/pages/loggedin_ourbarbers.php" class="text-gray-300 hover:text-white block">Our Barbers</a></li>
        <li><a href="/trimbook/dashboards/client_dashboard.php" class="text-gray-300 hover:text-white block">My Dashboard</a></li>
        <li class="pt-4 border-t border-gray-800">
          <span class="text-gray-400 text-sm block mb-3">Hi, <?= htmlspecialchars($first_name) ?></span>
          <a href="/trimbook/dashboards/client_dashboard.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold inline-block">
            My Account
          </a>
        </li>
      </ul>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="home" class="hero-gradient min-h-screen flex items-center justify-center px-6 pt-20">
    <div class="container mx-auto text-center max-w-5xl relative z-10">
      <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-8 animate-pulse">
        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
        <span class="text-sm font-medium text-blue-300">Welcome back, <?= htmlspecialchars($first_name) ?>!</span>
      </div>
      
      <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-tight mb-6 tracking-tight">
        READY FOR YOUR<br>
        <span class="gradient-text">NEXT TRIM?</span>
      </h1>
      
      <p class="text-lg md:text-xl text-gray-400 max-w-2xl mx-auto mb-12 leading-relaxed">
        Your perfect style is just a few clicks away. Book with our expert barbers and experience premium grooming.
      </p>
      
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="/trimbook/dashboards/client_selectBarber.php" class="w-full sm:w-auto inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          BOOK APPOINTMENT NOW
        </a>
        <a href="/trimbook/dashboards/client_dashboard.php" class="w-full sm:w-auto inline-block bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/20 transition">
          View My Bookings
        </a>
      </div>
    </div>
  </section>

  <!-- User Stats Section -->
  <section class="py-20 bg-zinc-950 border-y border-gray-800">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <div class="stat-card text-center p-8 rounded-2xl border border-gray-800 relative">
          <div class="text-5xl font-black gradient-text mb-3"><?= $total_appointments ?></div>
          <p class="text-gray-400 font-medium">Total Appointments</p>
        </div>
        <div class="stat-card text-center p-8 rounded-2xl border border-gray-800 relative">
          <div class="text-5xl font-black gradient-text mb-3"><?= $upcoming_appointments ?></div>
          <p class="text-gray-400 font-medium">Upcoming Bookings</p>
        </div>
        <div class="stat-card text-center p-8 rounded-2xl border border-gray-800 relative">
          <div class="text-5xl font-black gradient-text mb-3"><?= $expert_barbers ?>+</div>
          <p class="text-gray-400 font-medium">Expert Barbers</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Book With Us Section -->
  <section id="about" class="py-28 bg-black">
    <div class="container mx-auto px-6">
      <div class="text-center max-w-4xl mx-auto mb-20">
        <h2 class="text-4xl md:text-6xl font-black mb-6">Why Book With TrimBook?</h2>
        <p class="text-lg md:text-xl text-gray-400 leading-relaxed">
          Experience seamless scheduling, expert barbers, and premium grooming services — all in one place.
        </p>
      </div>
      
      <!-- Feature Cards -->
      <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <div class="text-center bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800 rounded-3xl p-10 card-hover">
          <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Instant Booking</h3>
          <p class="text-gray-400 leading-relaxed">Book your appointment in seconds with real-time availability</p>
        </div>
        <div class="text-center bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800 rounded-3xl p-10 card-hover">
          <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Skip The Wait</h3>
          <p class="text-gray-400 leading-relaxed">Arrive right on time with your scheduled appointment</p>
        </div>
        <div class="text-center bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800 rounded-3xl p-10 card-hover">
          <div class="w-20 h-20 bg-gradient-to-br from-pink-600 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Premium Service</h3>
          <p class="text-gray-400 leading-relaxed"><?= number_format($avg_rating, 1) ?> star rating from <?= $total_reviews ?>+ satisfied clients</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-28 bg-zinc-950">
    <div class="container mx-auto px-6">
      <div class="text-center mb-20">
        <h2 class="text-4xl md:text-6xl font-black mb-4">Our Services</h2>
        <p class="text-gray-400 text-lg">Premium grooming services tailored for you</p>
      </div>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
        
        <!-- Service 1: Classic Haircut -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="/trimbook/assets/images/classic.jpeg" alt="Classic Haircut" class="service-image w-full h-full object-cover">
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
            <p class="text-gray-400 leading-relaxed mb-4">Clean, sharp and timeless</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              35-45 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-black gradient-text">₱350</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 2: Fade & Line Up -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-pink-600/20"></div>
            <img src="/trimbook/assets/images/fade.jpeg" alt="Fade & Line Up" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Fade & Line Up</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Sharp fades with clean edges</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              35-45 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-black gradient-text">₱400</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 3: Kid's Haircut -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-600/20 to-orange-600/20"></div>
            <img src="/trimbook/assets/images/kid.jpeg" alt="Kid's Haircut" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Kid's Haircut</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-pink-600 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Gentle and stylish cuts for kids</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              20-30 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-black gradient-text">₱200</p>
              <a href="/trimbook/dashboards/client_selectBarber.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>
       
      </div>

      <div class="text-center mt-16">
        <a href="/trimbook/pages/loggedin_services.php" class="inline-block bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full text-base font-bold hover:bg-white/20 transition">
          View All Services
        </a>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-28 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 text-center">
      <div class="max-w-3xl mx-auto">
        <h2 class="text-4xl md:text-6xl font-black mb-6 leading-tight">Time For A Fresh Look?</h2>
        <p class="text-lg md:text-xl text-gray-400 mb-12 leading-relaxed">
          Book your appointment now and experience professional grooming at its finest.
        </p>
        <a href="/trimbook/dashboards/client_selectBarber.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-5 rounded-full text-lg font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          Schedule Appointment
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact" class="bg-zinc-950 border-t border-gray-800 py-16">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-4 gap-12 mb-12">
        <div class="md:col-span-2">
          <h3 class="text-3xl font-black mb-4 gradient-text">TRIMBOOK</h3>
          <p class="text-gray-400 leading-relaxed max-w-md">Your trusted barber appointment system. Book smarter, look sharper. Experience hassle-free scheduling with the best barbers in town.</p>
        </div>
        <div>
          <h4 class="font-bold mb-4 text-lg">Quick Links</h4>
          <ul class="space-y-3 text-sm">
            <li><a href="/trimbook/dashboards/client_dashboard.php" class="text-gray-400 hover:text-white transition">My Dashboard</a></li>
            <li><a href="/trimbook/pages/loggedin_services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="/trimbook/pages/loggedin_ourbarbers.php" class="text-gray-400 hover:text-white transition">Our Barbers</a></li>
            <li><a href="/trimbook/dashboards/client_selectBarber.php" class="text-gray-400 hover:text-white transition">Book Now</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-4 text-lg">Contact Us</h4>
          <ul class="space-y-3 text-sm text-gray-400">
            <li class="flex items-start">
              <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Baguio City, Philippines
            </li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-800 pt-8 text-center">
        <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    function toggleMobileMenu() {
      const menu = document.getElementById('mobileMenu');
      menu.classList.toggle('active');
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
            // Close mobile menu if open
            document.getElementById('mobileMenu').classList.remove('active');
          }
        }
      });
    });
  </script>

</body>
</html>
