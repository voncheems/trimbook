<?php
session_start();
require_once('../includes/dbconfig.php');

$errors = [];
$login_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login_input) || empty($password)) {
        $errors[] = 'Username/email and password required';
    } else {
        // Search for user by either username or email
        $sql = "SELECT user_id, first_name, last_name, username, email, password, user_type FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $login_input, $login_input);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Password correct - set session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // If barber, fetch and set barber_id
                    if ($user['user_type'] === 'barber') {
                        $barber_sql = "SELECT barber_id FROM barbers WHERE user_id = ?";
                        $barber_stmt = $conn->prepare($barber_sql);
                        $barber_stmt->bind_param("i", $user['user_id']);
                        $barber_stmt->execute();
                        $barber_result = $barber_stmt->get_result();
                        
                        if ($barber_result->num_rows == 1) {
                            $barber = $barber_result->fetch_assoc();
                            $_SESSION['barber_id'] = $barber['barber_id'];
                        }
                        $barber_stmt->close();
                    }
                    
                    // If admin, set admin session vars
                    if ($user['user_type'] === 'admin') {
                        $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['admin_username'] = $user['username'];
                    }
                    
                    $stmt->close();
                    $conn->close();
                    
                    // Redirect based on user type
                    if ($user['user_type'] === 'admin') {
                        header('Location: ../dashboards/admin_dashboard.php');
                    } elseif ($user['user_type'] === 'barber') {
                        header('Location: ../dashboards/barber_dashboard.php');
                    } else {
                        header('Location: ../dashboards/client_dashboard.php');
                    }
                    exit;
                } else {
                    $errors[] = 'Invalid username/email or password';
                }
            } else {
                $errors[] = 'Invalid username/email or password';
            }
            
            $stmt->close();
        }
    }
    $conn->close();
}

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

    .error-alert {
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

  <div class="min-h-screen flex flex-col items-center justify-center p-6">
    
    <!-- Logo -->
    <div class="mb-12">
      <a href="/trimbook/index.php" class="text-4xl font-black tracking-tight">TRIMBOOK</a>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md">
      
      <!-- Error Messages -->
      <?php if (!empty($errors)): ?>
        <div class="error-alert bg-red-500/20 border border-red-500/50 rounded-xl p-4 mb-6">
          <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
              <?php foreach ($errors as $error): ?>
                <p class="text-red-400 font-medium"><?= htmlspecialchars($error) ?></p>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="bg-zinc-900 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-semibold mb-8">Let's get you signed in</h2>

        <form method="POST" action="" class="space-y-5">
          
          <!-- Username or Email -->
          <div>
            <label for="username_or_email" class="block text-sm font-medium text-gray-300 mb-2">
              Username or Email
            </label>
            <input 
              type="text" 
              id="username_or_email" 
              name="username_or_email"
              value="<?= htmlspecialchars($login_input) ?>"
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
            <a href="../pages/forgot_password.php" class="text-sm text-blue-500 hover:text-blue-400">
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
