<?php
// At the top of EVERY protected page
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: /trimbook/pages/login_page.php');
    exit;
}

// Check if user has correct role for THIS page
if ($_SESSION['user_type'] !== 'customer') {
    header('Location: /unauthorized.php'); // or their correct dashboard
    exit;
}


// Get user data from session
$first_name = $_SESSION['first_name'] ?? 'Guest';
$last_name = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? 'guest';
$full_name = trim($first_name . ' ' . $last_name);

$title = "TrimBook | Your Barber Appointment System";
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
    }
    
    .card-hover {
      transition: all 0.3s ease;
    }
    
    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

  <!-- Navigation -->
  <header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <a href="/trimbook/pages/homepage_loggedin.php" class="text-2xl font-black tracking-tight">TRIMBOOK <span class="text-blue-500 text-sm">CLIENT</span></a>
      <ul class="hidden md:flex space-x-8 font-medium text-sm">
        <li><a href="#home" class="text-gray-300 hover:text-white transition">Home</a></li>
        <li><a href="../pages/loggedin_services.php" class="text-gray-300 hover:text-white transition">Services</a></li>
        <li><a href="../pages/loggedin_ourbarbers.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
        <li><a href="/trimbook/dashboards/client_dashboard.php" class="text-gray-300 hover:text-white transition">My Dashboard</a></li>
      </ul>
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-300 hidden lg:block">Hi, <span class="font-semibold text-white"><?= htmlspecialchars($first_name) ?></span></span>
        <a href="./pages/client_dashboard.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
          My Account
        </a>
      </div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section id="home" class="hero-gradient min-h-screen flex items-center justify-center px-6 pt-20">
    <div class="container mx-auto text-center max-w-5xl">
      <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-8">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-medium text-blue-300">Welcome back, <?= htmlspecialchars($first_name) ?>!</span>
      </div>
      
      <h1 class="text-5xl md:text-7xl font-black leading-tight mb-6 tracking-tight">
        READY FOR YOUR<br>
        <span class="gradient-text">NEXT TRIM?</span>
      </h1>
      
      <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
        Book your next appointment with top barbers in minutes. <br class="hidden md:block">
        Your perfect style is just a few clicks away.
      </p>
      
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="../dashboards/client_dashboard.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          Book Appointment Now
        </a>
        <a href="./pages/client_dashboard.php" class="inline-block bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/20 transition">
          View My Bookings
        </a>
      </div>
    </div>
  </section>

  <!-- Quick Stats Section -->
  <section class="py-16 bg-zinc-950 border-y border-gray-800">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto text-center">
        <div>
          <div class="text-4xl font-black gradient-text mb-2">50+</div>
          <p class="text-gray-400">Expert Barbers</p>
        </div>
        <div>
          <div class="text-4xl font-black gradient-text mb-2">10K+</div>
          <p class="text-gray-400">Happy Clients</p>
        </div>
        <div>
          <div class="text-4xl font-black gradient-text mb-2">24/7</div>
          <p class="text-gray-400">Online Booking</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-24 bg-black">
    <div class="container mx-auto px-6">
      <h2 class="text-4xl md:text-5xl font-black text-center mb-4">Our Services</h2>
      <p class="text-center text-gray-400 mb-16 text-lg">Premium grooming services tailored for you</p>
      
      <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Classic Haircut</h3>
          <p class="text-gray-400 leading-relaxed mb-4">Get a fresh, stylish haircut from expert barbers.</p>
          <p class="text-2xl font-bold gradient-text">₱250</p>
        </div>

        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Beard Trim</h3>
          <p class="text-gray-400 leading-relaxed mb-4">Shape and style your beard to perfection.</p>
          <p class="text-2xl font-bold gradient-text">₱150</p>
        </div>

        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-pink-600 to-orange-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Hot Towel Shave</h3>
          <p class="text-gray-400 leading-relaxed mb-4">Experience the classic barber shave treatment.</p>
          <p class="text-2xl font-bold gradient-text">₱200</p>
        </div>
        
      </div>

      <div class="text-center mt-12">
        <a href="./pages/Bookapp.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-base font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          Book a Service Now
        </a>
      </div>
    </div>
  </section>

  <!-- Barbers Section -->
  <section id="barbers" class="py-24 bg-zinc-950">
    <div class="container mx-auto px-6">
      <h2 class="text-4xl md:text-5xl font-black text-center mb-4">Meet Our Expert Barbers</h2>
      <p class="text-center text-gray-400 mb-16 text-lg">Skilled professionals dedicated to your style</p>
      
      <div class="grid md:grid-cols-4 gap-8 max-w-6xl mx-auto">
        <div class="text-center">
          <div class="w-32 h-32 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold">
            JD
          </div>
          <h3 class="text-xl font-bold mb-2">John Doe</h3>
          <p class="text-gray-400 text-sm">Master Barber</p>
        </div>
        <div class="text-center">
          <div class="w-32 h-32 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold">
            MS
          </div>
          <h3 class="text-xl font-bold mb-2">Mike Smith</h3>
          <p class="text-gray-400 text-sm">Senior Stylist</p>
        </div>
        <div class="text-center">
          <div class="w-32 h-32 bg-gradient-to-br from-pink-600 to-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold">
            RJ
          </div>
          <h3 class="text-xl font-bold mb-2">Robert Jones</h3>
          <p class="text-gray-400 text-sm">Fade Specialist</p>
        </div>
        <div class="text-center">
          <div class="w-32 h-32 bg-gradient-to-br from-orange-600 to-red-600 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold">
            TW
          </div>
          <h3 class="text-xl font-bold mb-2">Tom Wilson</h3>
          <p class="text-gray-400 text-sm">Beard Expert</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-24 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-4xl md:text-5xl font-black mb-6">Ready to Look Sharp?</h2>
      <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
        Book your appointment now and experience professional grooming at its finest.
      </p>
      <a href="./pages/Bookapp.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-10 py-5 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
        Schedule Your Appointment
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact" class="bg-zinc-950 border-t border-gray-800 py-12">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8 mb-8">
        <div>
          <h3 class="text-xl font-black mb-4">TRIMBOOK</h3>
          <p class="text-gray-400 text-sm">Your trusted barber appointment system</p>
        </div>
        <div>
          <h4 class="font-bold mb-4">Quick Links</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="./pages/client_dashboard.php" class="text-gray-400 hover:text-white transition">My Dashboard</a></li>
            <li><a href="./pages/Bookapp.php" class="text-gray-400 hover:text-white transition">Book Appointment</a></li>
            <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-4">Contact</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li>Email: info@trimbook.com</li>
            <li>Phone: (123) 456-7890</li>
            <li>Address: Baguio City</li>
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
