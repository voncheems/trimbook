<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Services | TrimBook</title>
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

    .service-image {
      transition: all 0.3s ease;
    }

    .card-hover:hover .service-image {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

<!-- Navigation - Consistent with index.php -->
<header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
  <nav class="container mx-auto flex justify-between items-center py-5 px-6">
    <a href="/trimbook/index.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
    <ul class="hidden md:flex space-x-8 font-medium text-sm">
      <li><a href="/trimbook/index.php" class="text-gray-300 hover:text-white transition">Home</a></li>
      <li><a href="/trimbook/index.php#about" class="text-gray-300 hover:text-white transition">About</a></li>
      <li><a href="/trimbook/pages/services.php" class="text-white font-semibold">Services</a></li>
      <li><a href="/trimbook/pages/ourBarbers_page.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
      <li><a href="/trimbook/index.php#contact" class="text-gray-300 hover:text-white transition">Contact</a></li>
    </ul>
    <div class="flex items-center space-x-4">
      <a href="/trimbook/pages/login_page.php" class="text-sm font-medium text-gray-300 hover:text-white transition">Login</a>
      <a href="/trimbook/pages/signup_page.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
        Sign Up
      </a>
    </div>
  </nav>
</header>

  <!-- Hero Section -->
  <section class="hero-gradient min-h-[40vh] flex items-center justify-center px-6 pt-32 pb-16">
    <div class="container mx-auto text-center max-w-4xl">
      <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-6">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-medium text-blue-300">Premium Grooming Services</span>
      </div>
      
      <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 tracking-tight">
        OUR <span class="gradient-text">SERVICES</span>
      </h1>
      
      <p class="text-lg text-gray-400 max-w-2xl mx-auto leading-relaxed">
        Premium grooming services tailored for you. Quality cuts, expert styling, unbeatable results.
      </p>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-24 bg-black">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
        
        <!-- Service 1 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
            <img src="classic-haircut.jpg" alt="Classic Haircut" class="service-image w-full h-full object-cover">
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
            <p class="text-gray-400 leading-relaxed mb-4">Clean, Sharp and Timeless</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              35-45 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold gradient-text">₱350</p>
              <a href="Bookapp.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 2 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-pink-600/20"></div>
            <img src="fade-lineup.jpg" alt="Fade & Line Up" class="service-image w-full h-full object-cover">
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
              <p class="text-3xl font-bold gradient-text">₱400</p>
              <a href="Bookapp.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 3 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-600/20 to-orange-600/20"></div>
            <img src="kids-haircut.jpg" alt="Kid's Haircut" class="service-image w-full h-full object-cover">
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
              <p class="text-3xl font-bold gradient-text">₱200</p>
              <a href="Bookapp.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 4 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/20 to-red-600/20"></div>
            <img src="shave-treatment.jpg" alt="Shave & Towel Treatment" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Shave & Towel</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Smooth shave with relaxing hot towel</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              25-35 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold gradient-text">₱300</p>
              <a href="Bookapp.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 5 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-pink-600/20"></div>
            <img src="modern-trendy.jpg" alt="Modern & Trendy Cuts" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Modern & Trendy</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Styled/Fresh cuts for the latest look</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              40-50 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold gradient-text">₱450</p>
              <a href="Bookapp.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

        <!-- Service 6 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-600/20 to-teal-600/20"></div>
            <img src="beard-grooming.jpg" alt="Beard Grooming & Design" class="service-image w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-2xl font-bold">Beard Grooming</h3>
              <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
              </div>
            </div>
            <p class="text-gray-400 leading-relaxed mb-4">Beard shaping, trimming & styling</p>
            <div class="flex items-center text-sm text-gray-400 mb-4">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              20-30 minutes
            </div>
            <div class="flex items-center justify-between">
              <p class="text-3xl font-bold gradient-text">₱250</p>
              <a href="Bookapp.php" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Book
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-20 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-black mb-4">Ready to Look Your Best?</h2>
      <p class="text-lg text-gray-400 max-w-xl mx-auto mb-8">
        Book your appointment now and experience premium grooming services.
      </p>
      <a href="../dashboards/client_selectBarber.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-10 py-4 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
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
            <li><a href="./pages/services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="./pages/ourBarbers_page.php" class="text-gray-400 hover:text-white transition">Our Barbers</a></li>
            <li><a href="../dashboards/client_selectBarber.php" class="text-gray-400 hover:text-white transition">Book Appointment</a></li>
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
