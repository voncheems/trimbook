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

require_once('../includes/dbconfig.php');

// Fetch all barbers from database with profile photos
$query = "
SELECT 
  b.barber_id,
  u.user_id,
  CONCAT(u.first_name, ' ', u.last_name) AS full_name,
  b.specialization,
  b.experience_years,
  u.profile_photo
FROM barbers b
JOIN users u ON b.user_id = u.user_id
WHERE u.user_type = 'barber'
ORDER BY b.barber_id ASC
";

$result = mysqli_query($conn, $query);

$title = "Our Barbers | TrimBook";
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

    /* Define gradient backgrounds as CSS classes */
    .gradient-blue-purple {
      background: linear-gradient(to bottom right, rgb(37, 99, 235), rgb(147, 51, 234));
    }
    
    .gradient-purple-pink {
      background: linear-gradient(to bottom right, rgb(147, 51, 234), rgb(219, 39, 119));
    }
    
    .gradient-pink-orange {
      background: linear-gradient(to bottom right, rgb(219, 39, 119), rgb(234, 88, 12));
    }
    
    .gradient-orange-red {
      background: linear-gradient(to bottom right, rgb(234, 88, 12), rgb(220, 38, 38));
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
        <li><a href="/trimbook/pages/loggedin_services.php" class="text-gray-300 hover:text-white transition">Services</a></li>
        <li><a href="/trimbook/pages/loggedin_ourbarbers.php" class="text-white transition font-semibold">Our Barbers</a></li>
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

  <!-- Hero Section -->
  <section class="min-h-[40vh] bg-gradient-to-b from-zinc-950 to-black flex items-center justify-center px-6 pt-32 pb-16">
    <div class="container mx-auto text-center max-w-4xl">
      <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-6">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-medium text-blue-300">All Available for Booking</span>
      </div>
      
      <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 tracking-tight">
        MEET OUR <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">EXPERT BARBERS</span>
      </h1>
      
      <p class="text-lg text-gray-400 max-w-2xl mx-auto leading-relaxed">
        Skilled professionals dedicated to your style. Choose the barber that fits you best.
      </p>
    </div>
  </section>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-black to-zinc-950 py-24 px-6">
    <div class="container mx-auto max-w-7xl">
      
      <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          
          <?php 
          $colors = [
            ['gradient' => 'gradient-blue-purple', 'badge_bg' => 'bg-blue-600/20', 'badge_text' => 'text-blue-300', 'badge_border' => 'border-blue-500/30', 'button' => 'gradient-blue-purple', 'shadow' => 'hover:shadow-purple-500/50'],
            ['gradient' => 'gradient-purple-pink', 'badge_bg' => 'bg-purple-600/20', 'badge_text' => 'text-purple-300', 'badge_border' => 'border-purple-500/30', 'button' => 'gradient-purple-pink', 'shadow' => 'hover:shadow-pink-500/50'],
            ['gradient' => 'gradient-pink-orange', 'badge_bg' => 'bg-pink-600/20', 'badge_text' => 'text-pink-300', 'badge_border' => 'border-pink-500/30', 'button' => 'gradient-pink-orange', 'shadow' => 'hover:shadow-orange-500/50'],
            ['gradient' => 'gradient-orange-red', 'badge_bg' => 'bg-orange-600/20', 'badge_text' => 'text-orange-300', 'badge_border' => 'border-orange-500/30', 'button' => 'gradient-orange-red', 'shadow' => 'hover:shadow-red-500/50']
          ];
          $colorIndex = 0;
          
          while($barber = mysqli_fetch_assoc($result)): 
            $color = $colors[$colorIndex % 4];
            $colorIndex++;
          ?>
          
          <!-- Barber Card -->
          <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8 text-center">
            <div class="relative mb-6">
              <div class="absolute inset-0 <?= $color['gradient'] ?> rounded-2xl blur-xl opacity-50"></div>
              <div class="relative w-40 h-40 bg-gray-700 rounded-2xl mx-auto border-4 border-gray-700 overflow-hidden">
                <?php 
                if (!empty($barber['profile_photo'])): 
                  $photoPath = '/trimbook/' . htmlspecialchars($barber['profile_photo']);
                ?>
                  <img src="<?= $photoPath ?>" 
                       alt="<?= htmlspecialchars($barber['full_name']) ?>" 
                       class="w-full h-full object-cover"
                       style="object-position: center 20%;"
                       onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                  <div style="display: none;" class="w-full h-full flex items-center justify-center bg-gray-700">
                    <svg class="w-20 h-20 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center bg-gray-700">
                    <svg class="w-20 h-20 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            
            <?php if ($barber['specialization']): ?>
            <div class="mb-3">
              <span class="inline-block <?= $color['badge_bg'] ?> <?= $color['badge_text'] ?> text-xs font-semibold px-3 py-1 rounded-full border <?= $color['badge_border'] ?>">
                <?= htmlspecialchars($barber['specialization']) ?>
              </span>
            </div>
            <?php endif; ?>
            
            <h3 class="text-2xl font-bold mb-2"><?= htmlspecialchars($barber['full_name']) ?></h3>
            
            <?php if ($barber['experience_years']): ?>
            <p class="text-gray-400 text-sm mb-4">
              <?= htmlspecialchars($barber['experience_years']) ?> year<?= $barber['experience_years'] > 1 ? 's' : '' ?> of experience
            </p>
            <?php else: ?>
            <p class="text-gray-400 text-sm mb-4">Professional barber</p>
            <?php endif; ?>
                   
            <a href="/trimbook/dashboards/client_booking.php?barber_id=<?= $barber['barber_id'] ?>" class="block w-full <?= $color['button'] ?> text-white py-3 rounded-xl font-semibold hover:shadow-lg <?= $color['shadow'] ?> transition">
              Book Now
            </a>
          </div>
          
          <?php endwhile; ?>
          
        </div>
      <?php else: ?>
        <!-- No Barbers Found -->
        <div class="text-center py-20">
          <svg class="w-24 h-24 text-gray-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <h3 class="text-2xl font-bold mb-2">No Barbers Available</h3>
          <p class="text-gray-400">Check back soon! Our team is growing.</p>
        </div>
      <?php endif; ?>
      
    </div>
  </main>

  <!-- CTA Section -->
  <section class="py-20 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-black mb-4">Ready to Get Started?</h2>
      <p class="text-lg text-gray-400 max-w-xl mx-auto mb-8">
        Book your appointment now and experience premium barbering services.
      </p>
      <a href="/trimbook/dashboards/client_selectBarber.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-base font-bold hover:shadow-lg hover:shadow-purple-500/50 transition">
        Book Appointment
      </a>
    </div>
  </section>

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
            <li><a href="/trimbook/pages/loggedin_services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="/trimbook/pages/loggedin_ourbarbers.php" class="text-gray-400 hover:text-white transition">Our Barbers</a></li>
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
