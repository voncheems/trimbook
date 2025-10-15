<?php
// login.php
$title = "Login | TrimBook";
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

    <!-- Login Card -->
    <div class="w-full max-w-md">
      <div class="bg-zinc-900 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-semibold mb-8">Let's get you signed in</h2>

        <form action="/trimbook/auth/process_login.php" method="POST" class="space-y-5">
          
          <!-- Username or Email -->
          <div>
            <label for="username_or_email" class="block text-sm font-medium text-gray-300 mb-2">
              Username or Email
            </label>
            <input 
              type="text" 
              id="username_or_email" 
              name="username_or_email" 
              required
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Username or Email"
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

          <!-- Forgot Password -->
          <div class="text-right">
            <a href="forgot_password.php" class="text-sm text-blue-500 hover:text-blue-400">
              Forgot password?
            </a>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
          >
            Login
          </button>
        </form>
      </div>

      <!-- Sign Up Link -->
      <p class="text-center text-gray-400 mt-6">
        Don't have a TRIMBOOK Account? <a href="signup_page.php" class="text-blue-500 hover:text-blue-400 font-medium">Sign up now</a>
      </p>
    </div>

  </div>

</body>
</html>
