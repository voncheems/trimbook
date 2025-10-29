<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | TrimBook</title>
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

    .dev-image {
      width: 100%;
      height: 192px;
      object-fit: cover;
      object-position: center;
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

<!-- Navigation -->
<header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
  <nav class="container mx-auto flex justify-between items-center py-5 px-6">
    <a href="/trimbook/index.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
    <ul class="hidden md:flex space-x-8 font-medium text-sm">
      <li><a href="/trimbook/index.php" class="text-gray-300 hover:text-white transition">Home</a></li>
      <li><a href="/trimbook/pages/about.php" class="text-white font-semibold">About</a></li>
      <li><a href="/trimbook/pages/services.php" class="text-gray-300 hover:text-white transition">Services</a></li>
      <li><a href="/trimbook/pages/ourBarbers_page.php" class="text-gray-300 hover:text-white transition">Our Barbers</a></li>
      <li><a href="/trimbook/pages/contact_page.php" class="text-gray-300 hover:text-white transition">Contact</a></li>
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
  <section class="hero-gradient min-h-[50vh] flex items-center justify-center px-6 pt-32 pb-16">
    <div class="container mx-auto text-center max-w-4xl">
      <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-6">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-medium text-blue-300">Our Story</span>
      </div>
      
      <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 tracking-tight">
        ABOUT <span class="gradient-text">TRIMBOOK</span>
      </h1>
      
      <p class="text-lg text-gray-400 max-w-2xl mx-auto leading-relaxed">
        A simple platform built to solve a real problem in the barbering industry. Learn about the vision behind TrimBook.
      </p>
    </div>
  </section>

  <!-- Our Story Section -->
  <section class="py-24 bg-black">
    <div class="container mx-auto px-6 max-w-5xl">
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <!-- Left Content -->
        <div>
          <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-4 py-2 mb-6">
            <span class="text-xs font-semibold text-blue-300 uppercase">Our Story</span>
          </div>
          
          <h2 class="text-4xl md:text-5xl font-black mb-6 leading-tight">
            The Problem We <span class="gradient-text">Wanted to Solve</span>
          </h2>
          
          <p class="text-gray-400 text-lg mb-4 leading-relaxed">
            Every day, customers struggled to book appointments with their favorite barbers. Long phone calls, missed availability, and scheduling confusion were common frustrations. Meanwhile, barbers wasted time managing bookings manually.
          </p>
          
          <p class="text-gray-400 text-lg mb-8 leading-relaxed">
            We decided there had to be a better way. TrimBook was created to bridge this gap—connecting customers with skilled barbers through a simple, seamless booking experience that saves time for everyone.
          </p>

          <a href="/trimbook/pages/ourBarbers_page.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
            Start Booking Now
          </a>
        </div>

        <!-- Right Visual -->
        <div class="relative">
          <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 rounded-3xl blur-xl opacity-20"></div>
          <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-12 flex flex-col items-center justify-center">
            <svg class="w-24 h-24 text-purple-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-center text-gray-300 text-lg font-semibold">Seamless booking for barbers and customers</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why We Built This Section -->
  <section class="py-24 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 max-w-5xl">
      <h2 class="text-4xl font-black text-center mb-6">Why We Built TrimBook</h2>
      <p class="text-center text-gray-400 text-lg mb-16 max-w-3xl mx-auto">
        Understanding the challenges that inspired our solution
      </p>

      <div class="space-y-6">
        <!-- Reason 1 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-8 flex gap-6">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-2">Wasted Time</h3>
            <p class="text-gray-400">Customers spent hours calling multiple barbershops just to find an available slot. We wanted to eliminate that hassle completely.</p>
          </div>
        </div>

        <!-- Reason 2 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-8 flex gap-6">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-purple-600/20 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-2">Unreliable Systems</h3>
            <p class="text-gray-400">Manual scheduling led to missed appointments, double bookings, and confusion. We needed a system that was reliable and automated.</p>
          </div>
        </div>

        <!-- Reason 3 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-8 flex gap-6">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-pink-600/20 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-2">Better Management</h3>
            <p class="text-gray-400">Barbers needed a smarter way to manage their schedules and serve their customers. Technology was the answer.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Vision Section -->
  <section class="py-24 bg-black">
    <div class="container mx-auto px-6 max-w-5xl">
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-12">
        <h2 class="text-4xl font-black mb-6">Our Vision</h2>
        
        <p class="text-lg text-gray-300 mb-6 leading-relaxed">
          We envision a world where booking a barber appointment is as simple and effortless as a few clicks. A platform where customers find their perfect barber, and barbers manage their time efficiently without the stress of manual scheduling.
        </p>
        
        <p class="text-lg text-gray-300 leading-relaxed">
          TrimBook is just the beginning. As we grow, we aim to set the standard for how service-based businesses connect with their customers. Innovation, reliability, and simplicity are at the heart of everything we do.
        </p>
      </div>
    </div>
  </section>

  <!-- Meet the Developers Section -->
  <section class="py-24 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 max-w-5xl">
      <h2 class="text-4xl font-black text-center mb-6">Behind the Scenes</h2>
      <p class="text-center text-gray-400 text-lg mb-16 max-w-3xl mx-auto">
        A passionate team of developers committed to building great solutions
      </p>

      <div class="grid md:grid-cols-3 gap-8">
        <!-- Developer 1  -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 blur-xl opacity-50"></div>
            <div class="relative w-full h-48 bg-gray-700 flex items-center justify-center overflow-hidden">
              <img src="../assets/images/ivan.png" alt="Developer !" class="dev-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
              <svg class="w-24 h-24 text-gray-500" style="display:none;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
          <div class="p-6 text-center">
            <h3 class="text-xl font-bold mb-1">Ivan Chen</h3>
            <p class="text-blue-400 font-semibold text-sm mb-3">Project Manager</p>
            <p class="text-gray-400 text-sm">Oversees project progress, team coordination, and final review.</p>
          </div>
        </div>
        
        <!-- Developer 2 - Middle Developer -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 blur-xl opacity-50"></div>
            <div class="relative w-full h-48 bg-gray-700 flex items-center justify-center overflow-hidden">
              <img src="../assets/images/cj.jpg" alt="Developer 2" class="dev-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
              <svg class="w-24 h-24 text-gray-500" style="display:none;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
          <div class="p-6 text-center">
            <h3 class="text-xl font-bold mb-1">Cj Cendana</h3>
            <p class="text-blue-400 font-semibold text-sm mb-3">Full Stack Developer</p>
            <p class="text-gray-400 text-sm">Passionate about building seamless user experiences and robust backend systems.</p>
          </div>
        </div>

        <!-- Developer 3 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 blur-xl opacity-50"></div>
            <div class="relative w-full h-48 bg-gray-700 flex items-center justify-center overflow-hidden">
              <img src="../assets/images/kenver.jpg" alt="Developer 3" class="dev-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
              <svg class="w-24 h-24 text-gray-500" style="display:none;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
          <div class="p-6 text-center">
            <h3 class="text-xl font-bold mb-1">Brenan Josh Cervantes</h3>
            <p class="text-purple-400 font-semibold text-sm mb-3">Database Administrator</p>
            <p class="text-gray-400 text-sm">Designs and manages the MySQL database.</p>
          </div>
        </div>

      <!-- Developer 4 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 blur-xl opacity-50"></div>
            <div class="relative w-full h-48 bg-gray-700 flex items-center justify-center overflow-hidden">
              <!-- Replace 'path/to/ivan-photo.jpg' with actual image path -->
              <img src="../assets/images/steph.jpg" alt="Developer 4" class="dev-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
              <svg class="w-24 h-24 text-gray-500" style="display:none;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
          <div class="p-6 text-center">
            <h3 class="text-xl font-bold mb-1">Stephanie Mabalot</h3>
            <p class="text-purple-400 font-semibold text-sm mb-3">UI/UX Designer / Documentation Specialist</p>
            <p class="text-gray-400 text-sm">Creates interface design and compiles reports and diagrams.</p>
          </div>
        </div>


        <!-- Developer 5 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-600 to-orange-600 blur-xl opacity-50"></div>
            <div class="relative w-full h-48 bg-gray-700 flex items-center justify-center overflow-hidden">
              <!-- Replace 'path/to/dev4-photo.jpg' with actual image path -->
              <img src="../assets/images/zel.jpg" alt="Developer 5" class="dev-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
              <svg class="w-24 h-24 text-gray-500" style="display:none;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
          <div class="p-6 text-center">
            <h3 class="text-xl font-bold mb-1">Denzel Manalo</h3>
            <p class="text-pink-400 font-semibold text-sm mb-3">QA Tester</p>
            <p class="text-gray-400 text-sm">Tests the system and ensures all requirements are met.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- CTA Section -->
  <section class="py-20 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-black mb-4">Ready to Experience TrimBook?</h2>
      <p class="text-lg text-gray-400 max-w-xl mx-auto mb-8">
        Join us in simplifying the way you book barber appointments.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/trimbook/pages/signup_page.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-base font-bold hover:shadow-lg hover:shadow-purple-500/50 transition">
          Get Started Now
        </a>
        <a href="/trimbook/pages/ourBarbers_page.php" class="inline-block border border-gray-600 text-white px-8 py-4 rounded-full text-base font-bold hover:bg-gray-900 transition">
          Browse Barbers
        </a>
      </div>
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
            <li><a href="/trimbook/pages/services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="/trimbook/pages/ourBarbers_page.php" class="text-gray-400 hover:text-white transition">Our Barbers</a></li>
            <li><a href="/trimbook/pages/about.php" class="text-gray-400 hover:text-white transition">About Us</a></li>
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
        <p class="text-gray-500 text-sm">&copy; <span id="year"></span> TrimBook. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>

</body>
</html>
