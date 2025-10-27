<?php
session_start();

// Get messages from session
$errors = $_SESSION['reset_errors'] ?? [];
$success = $_SESSION['reset_success'] ?? '';
$email_input = $_SESSION['reset_email'] ?? '';
$phone_input = $_SESSION['reset_phone'] ?? '';

// Clear session messages after retrieving them
unset($_SESSION['reset_errors']);
unset($_SESSION['reset_success']);
unset($_SESSION['reset_email']);
unset($_SESSION['reset_phone']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | TrimBook</title>
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

    .form-input {
      transition: all 0.3s ease;
    }

    .form-input:focus {
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
    }

    .alert {
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="bg-black text-white antialiased">

  <!-- Header -->
  <header class="bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <div class="flex items-center space-x-4">
        <!-- Back Arrow Button -->
        <a href="javascript:history.back()" class="text-white hover:text-purple-400 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </a>
        <a href="#" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black py-12 px-6">
    <div class="container mx-auto max-w-2xl">
      
      <!-- Page Header -->
      <div class="mb-10">
        <div class="flex items-center space-x-4 mb-4">
          <a href="javascript:history.back()" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </a>
          <h1 class="text-4xl md:text-5xl font-black">
            Forgot Password
          </h1>
        </div>
        <p class="text-gray-400 text-lg">Submit a password reset request to the admin</p>
      </div>

      <!-- Success Message -->
      <?php if ($success): ?>
        <div class="alert mb-8 bg-green-500/10 border border-green-500/50 rounded-2xl p-6">
          <div class="flex items-start">
            <svg class="w-6 h-6 text-green-500 mt-0.5 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
              <p class="text-lg font-semibold text-green-400 mb-1">Request Submitted!</p>
              <p class="text-sm text-green-300/80"><?= htmlspecialchars($success) ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Error Messages -->
      <?php if (!empty($errors)): ?>
        <div class="alert mb-8 bg-red-500/10 border border-red-500/50 rounded-2xl p-6">
          <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mt-0.5 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
              <p class="text-lg font-semibold text-red-400 mb-1">Submission Failed</p>
              <?php foreach ($errors as $error): ?>
                <p class="text-sm text-red-300/80"><?= htmlspecialchars($error) ?></p>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Form Card -->
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-bold">Password Reset Request</h2>
        </div>

        <!-- Form -->
        <form action="../auth/submit_reset_request.php" method="POST" class="p-8 space-y-6">
          
          <!-- Information Box -->
          <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-6">
            <div class="flex items-start">
              <svg class="w-6 h-6 text-blue-400 mt-0.5 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <div>
                <p class="text-sm text-blue-300">
                  Enter your account information below. An admin will review your request and contact you to help reset your password.
                </p>
              </div>
            </div>
          </div>

          <!-- Email Input -->
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
              Email Address <span class="text-red-400">*</span>
            </label>
            <input 
              type="email" 
              id="email" 
              name="email"
              value="<?= htmlspecialchars($email_input) ?>"
              required
              class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
              placeholder="your.email@example.com"
            >
          </div>

          <!-- Phone Number Input -->
          <div>
            <label for="phone" class="block text-sm font-semibold text-gray-300 mb-2">
              Phone Number <span class="text-gray-500 text-xs font-normal">(Optional)</span>
            </label>
            <input 
              type="tel" 
              id="phone" 
              name="phone"
              value="<?= htmlspecialchars($phone_input) ?>"
              class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500"
              placeholder="+1 (555) 123-4567"
            >
            <p class="mt-2 text-xs text-gray-500">Providing your phone number helps the admin contact you faster</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-700">
            <a 
              href="login_page.php" 
              class="text-sm text-gray-400 hover:text-purple-400 transition font-medium"
            >
              Remember your password? <span class="text-purple-400">Sign In</span>
            </a>
            <div class="flex items-center space-x-4 w-full sm:w-auto">
              <a 
                href="javascript:history.back()" 
                class="flex-1 sm:flex-none px-8 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition text-center"
              >
                Cancel
              </a>
              <button 
                type="submit" 
                class="flex-1 sm:flex-none px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105"
              >
                Submit Request
              </button>
            </div>
          </div>

        </form>
      </div>

      <!-- Additional Help -->
      <div class="mt-8 text-center">
        <p class="text-gray-500 text-sm">
          Need help? <a href="contact_page.php" class="text-purple-400 hover:text-purple-300 font-medium">Contact Support</a>
        </p>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center">
    <p class="text-gray-500 text-sm">&copy; 2024 TrimBook. All Rights Reserved.</p>
  </footer>

</body>
</html>
