<?php
require_once('../includes/dbconfig.php');

// Get contact information
$contact = [];
$contact_result = $conn->query("SELECT * FROM trimbook_contact LIMIT 1");
if ($contact_result) {
    $contact = $contact_result->fetch_assoc();
}

$title = "Contact Us | TrimBook";
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

  <!-- Navbar -->
  <header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <a href="index.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
      <ul class="hidden md:flex space-x-8 font-medium text-sm">
        <li><a href="../index.php" class="text-gray-300 hover:text-white transition">Home</a></li>
        <li><a href="../pages/about.php" class="text-gray-300 hover:text-white transition">About</a></li>
        <li><a href="../pages/services.php" class="text-gray-300 hover:text-white transition">Services</a></li>
        <li><a href="../pages/ourBarbers_page.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
        <li><a href="../pages/contact_page.php" class="text-white transition font-semibold">Contact</a></li>
      </ul>
      <div class="flex items-center space-x-4">
        <a href="../pages/login_page.php" class="text-sm font-medium text-gray-300 hover:text-white transition">Login</a>
        <a href="../pages/signup_page.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
          Sign Up
        </a>
      </div>
    </nav>
  </header>

  <!-- Contact Information Section -->
  <section class="py-24 bg-gradient-to-b from-zinc-950 to-black pt-32">
    <div class="container mx-auto px-6 max-w-6xl">
      <div class="text-center mb-16">
        <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-6">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
          <span class="text-sm font-medium text-blue-300">Get In Touch</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-black mb-4">
          Contact <span class="gradient-text">Information</span>
        </h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">
          Multiple ways to reach us. We're here to help with your appointment needs.
        </p>
      </div>
      
      <div class="grid md:grid-cols-2 gap-8">
        
        <!-- Contact Cards -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Email</h3>
          <?php if (!empty($contact['email'])): ?>
            <p class="text-gray-400 leading-relaxed mb-4">Send us an email anytime</p>
            <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="text-blue-400 font-semibold hover:text-blue-300 transition text-lg">
              <?= htmlspecialchars($contact['email']) ?>
            </a>
          <?php else: ?>
            <p class="text-gray-500">Email information not available</p>
          <?php endif; ?>
        </div>

        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.73 1.365a1 1 0 001.27-1.27l-1.365-2.73a1 1 0 00.756-.502l4.493-1.498a1 1 0 00.684-.948V5a2 2 0 00-2-2h-7.5a2 2 0 00-2 2v12a2 2 0 002 2h7.5a2 2 0 002-2v-2.5"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Phone</h3>
          <?php if (!empty($contact['phone'])): ?>
            <p class="text-gray-400 leading-relaxed mb-4">Call us during business hours</p>
            <a href="tel:<?= htmlspecialchars($contact['phone']) ?>" class="text-blue-400 font-semibold hover:text-blue-300 transition text-lg">
              <?= htmlspecialchars($contact['phone']) ?>
            </a>
          <?php else: ?>
            <p class="text-gray-500">Phone information not available</p>
          <?php endif; ?>
        </div>

        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-pink-600 to-orange-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Address</h3>
          <?php if (!empty($contact['address'])): ?>
            <p class="text-gray-400 leading-relaxed mb-4">Visit us at our location</p>
            <p class="text-blue-400 font-semibold text-lg">
              <?= htmlspecialchars($contact['address']) ?>
            </p>
          <?php else: ?>
            <p class="text-gray-500">Address information not available</p>
          <?php endif; ?>
        </div>

        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <div class="w-16 h-16 bg-gradient-to-br from-orange-600 to-red-600 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-3">Business Hours</h3>
          <?php if (!empty($contact['hours'])): ?>
            <p class="text-gray-400 leading-relaxed mb-4">We're open during these hours</p>
            <p class="text-blue-400 font-semibold text-lg">
              <?= htmlspecialchars($contact['hours']) ?>
            </p>
          <?php else: ?>
            <p class="text-gray-500">Hours information not available</p>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </section>

  <!-- Quick Contact Section -->
  <section class="py-24 bg-zinc-950">
    <div class="container mx-auto px-6 text-center max-w-3xl">
      <h2 class="text-4xl md:text-5xl font-black mb-6">Ready to Book?</h2>
      <p class="text-gray-400 text-lg mb-10">Have any questions before booking? Feel free to reach out through any of the methods above, or book your appointment directly.</p>
      
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="../pages/login_page.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          Book Now
        </a>
        <a href="../pages/services.php" class="inline-block bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/20 transition">
          View Services
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-12">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8 mb-8 max-w-5xl mx-auto">
        <div>
          <h3 class="text-xl font-black mb-4">TRIMBOOK</h3>
          <p class="text-gray-400 text-sm">Your trusted barber appointment system. Book smarter, look sharper.</p>
        </div>
        <div>
          <h4 class="font-bold mb-4">Quick Links</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="../index.php" class="text-gray-400 hover:text-white transition">Home</a></li>
            <li><a href="../pages/services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="../pages/ourBarbers.php" class="text-gray-400 hover:text-white transition">Our Barbers</a></li>
            <li><a href="../pages/contact_page.php" class="text-gray-400 hover:text-white transition">Contact</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-4">Contact</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <?php if (!empty($contact['email'])): ?>
              <li>Email: <?= htmlspecialchars($contact['email']) ?></li>
            <?php endif; ?>
            <?php if (!empty($contact['phone'])): ?>
              <li>Phone: <?= htmlspecialchars($contact['phone']) ?></li>
            <?php endif; ?>
            <?php if (!empty($contact['address'])): ?>
              <li>Address: <?= htmlspecialchars($contact['address']) ?></li>
            <?php endif; ?>
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
