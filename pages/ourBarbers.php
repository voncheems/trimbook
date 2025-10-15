<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meet Our Barbers | TrimBook</title>
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
      <a href="index.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
      <ul class="hidden md:flex space-x-8 font-medium text-sm">
        <li><a href="index.php#home" class="text-gray-300 hover:text-white transition">Home</a></li>
        <li><a href="index.php#about" class="text-gray-300 hover:text-white transition">About</a></li>
        <li><a href="index.php#services" class="text-gray-300 hover:text-white transition">Services</a></li>
        <li><a href="#barbers" class="text-white font-semibold">Our Barbers</a></li>
        <li><a href="index.php#contact" class="text-gray-300 hover:text-white transition">Contact</a></li>
      </ul>
      <div class="flex items-center space-x-4">
        <a href="../pages/login_page.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
          Book Now
        </a>
      </div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="hero-gradient min-h-[40vh] flex items-center justify-center px-6 pt-32 pb-16">
    <div class="container mx-auto text-center max-w-4xl">
      <div class="inline-flex items-center space-x-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-6 py-2 mb-6">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-medium text-blue-300">All Available for Booking</span>
      </div>
      
      <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 tracking-tight">
        MEET OUR <span class="gradient-text">EXPERT BARBERS</span>
      </h1>
      
      <p class="text-lg text-gray-400 max-w-2xl mx-auto leading-relaxed">
        Skilled professionals dedicated to your style. Choose the barber that fits you best.
      </p>
    </div>
  </section>

  <!-- Barbers Section -->
  <section id="barbers" class="py-24 bg-black">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
        
        <!-- Barber 1 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8 text-center">
          <div class="relative mb-6">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl blur-xl opacity-50"></div>
            <img src="ivan.jpg" class="relative w-40 h-40 object-cover rounded-2xl mx-auto border-4 border-gray-700" alt="The Classic Master">
          </div>
          <div class="mb-3">
            <span class="inline-block bg-blue-600/20 text-blue-300 text-xs font-semibold px-3 py-1 rounded-full border border-blue-500/30">
              The Classic Master
            </span>
          </div>
          <h3 class="text-2xl font-bold mb-2 h-8">Barber Ivan</h3>
          <p class="text-gray-400 text-sm mb-4 h-10">Traditional cuts with modern precision</p>
          <div class="flex justify-center items-center space-x-1 mb-6">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <span class="text-lg font-bold text-white">4.9</span>
            <span class="text-sm text-gray-400">(127 reviews)</span>
          </div>
          <a href="Bookapp.php" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition">
            Book Now
          </a>
        </div>

        <!-- Barber 2 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8 text-center">
          <div class="relative mb-6">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl blur-xl opacity-50"></div>
            <img src="denzel1.jpg" class="relative w-40 h-40 object-cover rounded-2xl mx-auto border-4 border-gray-700" alt="The Fade Specialist">
          </div>
          <div class="mb-3">
            <span class="inline-block bg-purple-600/20 text-purple-300 text-xs font-semibold px-3 py-1 rounded-full border border-purple-500/30">
              The Fade Specialist
            </span>
          </div>
          <h3 class="text-2xl font-bold mb-2 h-8">Barber Denzel</h3>
          <p class="text-gray-400 text-sm mb-4 h-10">Perfect fades every single time</p>
          <div class="flex justify-center items-center space-x-1 mb-6">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <span class="text-lg font-bold text-white">5.0</span>
            <span class="text-sm text-gray-400">(203 reviews)</span>
          </div>
          <a href="Bookapp.php" class="block w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-pink-500/50 transition">
            Book Now
          </a>
        </div>

        <!-- Barber 3 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8 text-center">
          <div class="relative mb-6">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-600 to-orange-600 rounded-2xl blur-xl opacity-50"></div>
            <img src="brenan.jpg" class="relative w-40 h-40 object-cover rounded-2xl mx-auto border-4 border-gray-700" alt="The Style Innovator">
          </div>
          <div class="mb-3">
            <span class="inline-block bg-pink-600/20 text-pink-300 text-xs font-semibold px-3 py-1 rounded-full border border-pink-500/30">
              The Style Innovator
            </span>
          </div>
          <h3 class="text-2xl font-bold mb-2 h-8">Barber Brenan</h3>
          <p class="text-gray-400 text-sm mb-4 h-10">Trendy styles and creative designs</p>
          <div class="flex justify-center items-center space-x-1 mb-6">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <span class="text-lg font-bold text-white">4.8</span>
            <span class="text-sm text-gray-400">(156 reviews)</span>
          </div>
          <a href="Bookapp.php" class="block w-full bg-gradient-to-r from-pink-600 to-orange-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-orange-500/50 transition">
            Book Now
          </a>
        </div>

        <!-- Barber 4 -->
        <div class="card-hover bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8 text-center">
          <div class="relative mb-6">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600 to-red-600 rounded-2xl blur-xl opacity-50"></div>
            <img src="denzel.jpg" class="relative w-40 h-40 object-cover rounded-2xl mx-auto border-4 border-gray-700" alt="The Grooming Expert">
          </div>
          <div class="mb-3">
            <span class="inline-block bg-orange-600/20 text-orange-300 text-xs font-semibold px-3 py-1 rounded-full border border-orange-500/30">
              The Grooming Expert
            </span>
          </div>
          <h3 class="text-2xl font-bold mb-2 h-8">Barber Denzel</h3>
          <p class="text-gray-400 text-sm mb-4 h-10">Full grooming and beard specialist</p>
          <div class="flex justify-center items-center space-x-1 mb-6">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <span class="text-lg font-bold text-white">4.9</span>
            <span class="text-sm text-gray-400">(189 reviews)</span>
          </div>
          <a href="Bookapp.php" class="block w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-red-500/50 transition">
            Book Now
          </a>
        </div>

      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-20 bg-gradient-to-b from-black to-zinc-950">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-black mb-4">Can't Decide?</h2>
      <p class="text-lg text-gray-400 max-w-xl mx-auto mb-8">
        Let our system match you with the perfect barber based on your preferences.
      </p>
      <a href="Bookapp.php" class="inline-block bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full text-base font-bold hover:bg-white/20 transition">
        Get Matched Now
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
            <li><a href="index.php#services" class="text-gray-400 hover:text-white transition">Services</a></li>
            <li><a href="#barbers" class="text-gray-400 hover:text-white transition">Our Barbers</a></li>
            <li><a href="Bookapp.php" class="text-gray-400 hover:text-white transition">Book Appointment</a></li>
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
