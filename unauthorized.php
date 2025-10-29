<?php
// Start session to check user status
session_start();

// Redirect after 5 seconds
$redirect_url = '/trimbook/index.php';
$redirect_delay = 5000; // milliseconds

$title = "Unauthorized Access | TrimBook";
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
    
    .error-gradient {
      background: radial-gradient(ellipse at center, #1a1a2e 0%, #0a0a0f 100%);
      position: relative;
    }
    
    .error-gradient::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 50% 50%, rgba(239, 68, 68, 0.1) 0%, transparent 50%);
      pointer-events: none;
    }

    .pulse-ring {
      animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse-ring {
      0%, 100% {
        opacity: 1;
        transform: scale(1);
      }
      50% {
        opacity: 0.5;
        transform: scale(1.1);
      }
    }

    .countdown {
      font-variant-numeric: tabular-nums;
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

  <!-- Unauthorized Access Section -->
  <section class="error-gradient min-h-screen flex items-center justify-center px-6">
    <div class="container mx-auto text-center max-w-3xl relative z-10">
      
      <!-- Error Icon -->
      <div class="mb-8 relative inline-block">
        <div class="pulse-ring absolute inset-0 rounded-full border-4 border-red-500/30"></div>
        <div class="relative w-32 h-32 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-red-500/50">
          <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
      </div>

      <!-- Error Message -->
      <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight">
        ACCESS <span class="text-red-500">DENIED</span>
      </h1>
      
      <div class="inline-flex items-center space-x-2 bg-red-600/20 border border-red-500/30 rounded-full px-6 py-2 mb-8">
        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-medium text-red-300">401 Unauthorized</span>
      </div>

      <p class="text-lg md:text-xl text-gray-400 mb-8 leading-relaxed">
        You don't have permission to access this page. This area is restricted to authorized users only.
      </p>

      <!-- Reason/Details -->
      <div class="bg-white/5 border border-gray-800 rounded-2xl p-6 mb-8 max-w-xl mx-auto">
        <p class="text-gray-300 text-sm leading-relaxed">
          <strong class="text-white">Possible reasons:</strong><br>
          • You are not logged in<br>
          • Your account doesn't have the required permissions<br>
          • Your session has expired<br>
          • You're trying to access a restricted area
        </p>
      </div>

      <!-- Countdown Timer -->
      <div class="mb-8">
        <p class="text-gray-400 text-sm mb-2">Redirecting to homepage in</p>
        <div class="countdown text-4xl font-black gradient-text">
          <span id="countdown">5</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="<?= $redirect_url ?>" class="w-full sm:w-auto inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105">
          Go to Homepage
        </a>
        <a href="/trimbook/pages/login_page.php" class="w-full sm:w-auto inline-block bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/20 transition">
          Login
        </a>
      </div>

      <!-- Additional Help -->
      <div class="mt-12 text-center">
        <p class="text-gray-500 text-sm">
          Need help? Contact us at <a href="mailto:info@trimbook.com" class="text-blue-400 hover:text-blue-300 transition">info@trimbook.com</a>
        </p>
      </div>

    </div>
  </section>

  <script>
    // Countdown timer
    let seconds = <?= $redirect_delay / 1000 ?>;
    const countdownElement = document.getElementById('countdown');
    
    const countdownInterval = setInterval(() => {
      seconds--;
      countdownElement.textContent = seconds;
      
      if (seconds <= 0) {
        clearInterval(countdownInterval);
        window.location.href = '<?= $redirect_url ?>';
      }
    }, 1000);

    // Auto redirect after delay
    setTimeout(() => {
      window.location.href = '<?= $redirect_url ?>';
    }, <?= $redirect_delay ?>);
  </script>

</body>
</html>
