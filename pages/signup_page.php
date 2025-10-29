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
                First Name*
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
                Last Name*
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
              Email*
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
              Phone Number (Optional)
            </label>
            <input 
              type="text" 
              id="phone" 
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="11"
              name="phone_no" 
              class="w-full px-4 py-3 bg-zinc-800 text-white rounded-lg border border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-500"
              placeholder="Phone Number (11 digits)"
            >
            <p id="phoneError" class="text-red-500 text-sm mt-2 hidden">Phone number must be exactly 11 digits</p>
          </div>

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
              Username*
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
              Password*
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
              Confirm Password*
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
              I agree to the <a href="#" onclick="openTermsModal(); return false;" class="text-blue-500 hover:text-blue-400 underline">Terms of Services</a>
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

  <!-- Terms & Privacy Modal -->
  <div id="termsModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-zinc-900 rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl">
      
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-zinc-800">
        <h2 class="text-2xl font-bold">Terms & Privacy</h2>
        <button onclick="closeTermsModal()" class="text-gray-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Tab Navigation -->
      <div class="flex space-x-4 px-6 pt-4 border-b border-zinc-800">
        <button 
          onclick="showModalTab('terms')" 
          id="modalTermsTab"
          class="px-4 py-3 font-medium border-b-2 border-blue-500 text-blue-500 transition"
        >
          Terms of Service
        </button>
        <button 
          onclick="showModalTab('privacy')" 
          id="modalPrivacyTab"
          class="px-4 py-3 font-medium border-b-2 border-transparent text-gray-400 hover:text-white transition"
        >
          Privacy Policy
        </button>
      </div>

      <!-- Modal Content -->
      <div class="flex-1 overflow-y-auto p-6">
        
        <!-- Terms of Service Content -->
        <div id="modalTermsContent" class="space-y-6">
          
          <section>
            <h3 class="text-xl font-semibold mb-3">1. Acceptance of Terms</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              By accessing and using TrimBook, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">2. User Accounts</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">
              When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms.
            </p>
            <p class="text-gray-300 text-sm leading-relaxed">
              You are responsible for safeguarding the password that you use to access the service and for any activities or actions under your password.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">3. User Content</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">
              You retain all rights to the content you post on TrimBook. By posting content, you grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, and display your content.
            </p>
            <p class="text-gray-300 text-sm leading-relaxed">
              You are solely responsible for the content you post and the consequences of posting or publishing it.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">4. Prohibited Conduct</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">You agree not to:</p>
            <ul class="space-y-1.5 text-gray-300 text-sm ml-6">
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Use the service for any illegal purpose</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Harass, abuse, or harm another person or group</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Post false, inaccurate, or misleading content</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Upload or transmit viruses or malicious code</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Attempt to gain unauthorized access to the service</span>
              </li>
            </ul>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">5. Intellectual Property</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              The service and its original content, features, and functionality are and will remain the exclusive property of TrimBook and its licensors.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">6. Termination</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              We may terminate or suspend your account immediately, without prior notice or liability, for any reason whatsoever, including if you breach the Terms.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">7. Limitation of Liability</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              In no event shall TrimBook be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, data, or goodwill.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">8. Contact Us</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              Questions? Contact us at <a href="mailto:support@trimbook.com" class="text-blue-500 hover:text-blue-400">support@trimbook.com</a>
            </p>
          </section>

        </div>

        <!-- Privacy Policy Content -->
        <div id="modalPrivacyContent" class="space-y-6 hidden">
          
          <section>
            <h3 class="text-xl font-semibold mb-3">1. Information We Collect</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">
              We collect information that you provide directly to us when you create an account:
            </p>
            <ul class="space-y-1.5 text-gray-300 text-sm ml-6">
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Personal information (name, email, phone number)</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Account credentials (username and password)</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Content you post or share on the platform</span>
              </li>
            </ul>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">2. How We Use Your Information</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">We use the information we collect to:</p>
            <ul class="space-y-1.5 text-gray-300 text-sm ml-6">
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Provide, maintain, and improve our services</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Process your transactions and send related information</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Send you technical notices and support messages</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Monitor and analyze trends and activities</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Detect, prevent, and address security issues</span>
              </li>
            </ul>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">3. Information Sharing</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">
              We do not sell your personal information. We may share your information:
            </p>
            <ul class="space-y-1.5 text-gray-300 text-sm ml-6">
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>With your consent or at your direction</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>With service providers who perform services on our behalf</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>To comply with legal obligations</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>To protect the rights and safety of TrimBook and users</span>
              </li>
            </ul>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">4. Data Security</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              We implement appropriate technical and organizational measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">5. Your Rights</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-2">You have the right to:</p>
            <ul class="space-y-1.5 text-gray-300 text-sm ml-6">
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Access and receive a copy of your personal information</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Correct or update your personal information</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Delete your personal information</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Object to or restrict certain processing</span>
              </li>
            </ul>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">6. Cookies and Tracking</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              We use cookies and similar tracking technologies to track activity on our service. You can instruct your browser to refuse all cookies.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">7. Children's Privacy</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              Our service is not intended for users under 13. We do not knowingly collect personal information from children under 13.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">8. Changes to Privacy Policy</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.
            </p>
          </section>

          <section>
            <h3 class="text-xl font-semibold mb-3">9. Contact Us</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              Questions? Contact us at <a href="mailto:privacy@trimbook.com" class="text-blue-500 hover:text-blue-400">privacy@trimbook.com</a>
            </p>
          </section>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="p-6 border-t border-zinc-800">
        <p class="text-xs text-gray-400 text-center">Last updated: October 29, 2025</p>
      </div>

    </div>
  </div>

  <script>
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const registerForm = document.getElementById('registerForm');

    // Handle phone input
    phoneInput.addEventListener('input', function () {
      // Remove any non-digit character as the user types or pastes
      this.value = this.value.replace(/\D/g, '');
      validatePhone();
    });

    phoneInput.addEventListener('blur', function () {
      validatePhone();
    });

    function validatePhone() {
      const phoneValue = phoneInput.value;
      
      // If phone is provided but not 11 digits, show error
      if (phoneValue.length > 0 && phoneValue.length !== 11) {
        phoneError.classList.remove('hidden');
        phoneInput.classList.add('border-red-500');
        return false;
      } else {
        phoneError.classList.add('hidden');
        phoneInput.classList.remove('border-red-500');
        return true;
      }
    }

    // Handle form submission
    registerForm.addEventListener('submit', function(e) {
      // Validate passwords match
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }

      // Validate phone if provided
      if (!validatePhone()) {
        e.preventDefault();
        alert('Please enter a valid 11-digit phone number or leave it empty');
        return false;
      }
    });

    // Block numbers from first and last name
    function blockNumbersOnly(input) {
      input.addEventListener('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
      });
    }

    blockNumbersOnly(document.getElementById('first_name'));
    blockNumbersOnly(document.getElementById('last_name'));

    // Terms Modal Functions
    function openTermsModal() {
      document.getElementById('termsModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeTermsModal() {
      document.getElementById('termsModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    function showModalTab(tab) {
      const termsTab = document.getElementById('modalTermsTab');
      const privacyTab = document.getElementById('modalPrivacyTab');
      const termsContent = document.getElementById('modalTermsContent');
      const privacyContent = document.getElementById('modalPrivacyContent');

      if (tab === 'terms') {
        termsTab.classList.remove('border-transparent', 'text-gray-400');
        termsTab.classList.add('border-blue-500', 'text-blue-500');
        privacyTab.classList.remove('border-blue-500', 'text-blue-500');
        privacyTab.classList.add('border-transparent', 'text-gray-400');
        
        termsContent.classList.remove('hidden');
        privacyContent.classList.add('hidden');
      } else {
        privacyTab.classList.remove('border-transparent', 'text-gray-400');
        privacyTab.classList.add('border-blue-500', 'text-blue-500');
        termsTab.classList.remove('border-blue-500', 'text-blue-500');
        termsTab.classList.add('border-transparent', 'text-gray-400');
        
        privacyContent.classList.remove('hidden');
        termsContent.classList.add('hidden');
      }
    }

    // Close modal when clicking outside
    document.getElementById('termsModal')?.addEventListener('click', function(e) {
      if (e.target === this) {
        closeTermsModal();
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !document.getElementById('termsModal').classList.contains('hidden')) {
        closeTermsModal();
      }
    });
  </script>

</body>
</html>
