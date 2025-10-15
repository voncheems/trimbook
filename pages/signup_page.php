<?php
// register.php
$title = "Sign Up | TrimBook";
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
  </style>
  
</head>
<body class="bg-black text-white antialiased">

  <div class="min-h-screen flex flex-col items-center justify-center p-6">
    
    <!-- Logo -->
    <div class="mb-12">
      <a href="/trimbook/index.php" class="text-4xl font-black tracking-tight">TRIMBOOK</a>
    </div>

    <!-- Sign Up Card -->
    <div class="w-full max-w-md">
      <div class="bg-zinc-900 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-semibold mb-8">Create your account</h2>

        <form id="registerForm" action="/trimbook/auth/process_register.php" method="POST" class="space-y-5">
          
          <!-- First Name and Last Name -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">
                First Name
              </label>
              <input 
                type="text" 
                id="first_name" 
                name="first_name" 
                required
                class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
                placeholder="First Name"
              >
            </div>
            <div>
              <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">
                Last Name
              </label>
              <input 
                type="text" 
                id="last_name" 
                name="last_name" 
                required
                class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
                placeholder="Last Name"
              >
            </div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
              Email
            </label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              required
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Email"
            >
          </div>

          <!-- Phone Number -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
              Phone Number
            </label>
            <input 
              type="tel" 
              id="phone" 
              name="phone_no" 
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Phone Number"
            >
          </div>

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
              Username
            </label>
            <input 
              type="text" 
              id="username" 
              name="username" 
              required
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Username"
            >
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
              Password
            </label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              required
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Password"
            >
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">
              Confirm Password
            </label>
            <input 
              type="password" 
              id="confirm_password" 
              name="confirm_password" 
              required
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Confirm Password"
            >
          </div>

          <!-- Terms of Service -->
          <div class="flex items-start space-x-2">
            <input 
              type="checkbox" 
              id="terms" 
              name="terms" 
              required
              class="mt-1 w-4 h-4 rounded border-gray-600 text-blue-600 focus:ring-blue-500"
            >
            <label for="terms" class="text-sm text-gray-300">
              I agree to the <a href="terms.php" class="text-blue-500 hover:text-blue-400 underline">Terms of Services</a>
            </label>
          </div>

          <button 
            type="submit"
            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
          >
            Sign Up
          </button>
        </form>

        <!-- Login Link -->
        <p class="text-center text-gray-400 mt-6">
          Already have a TRIMBOOK Account? <a href="login_page.php" class="text-blue-500 hover:text-blue-400 font-medium">Log in</a>
        </p>
      </div>
    </div>

  </div>

  <script>
    // Handle form submission and redirect to client dashboard
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      // Validate passwords match
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }

    });
  </script>

</body>
</html>
