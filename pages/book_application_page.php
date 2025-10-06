<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Appointment | TrimBook</title>
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
    
    .service-card {
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .service-card:hover {
      transform: translateX(4px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .service-card.selected {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .time-btn {
      transition: all 0.3s ease;
    }

    .time-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .time-btn.selected {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-color: #667eea;
    }

    .calendar-day {
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .calendar-day:hover {
      background: rgba(102, 126, 234, 0.2);
    }

    .calendar-day.selected {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .calendar-day.disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }
  </style>
</head>
<body class="bg-black text-white antialiased min-h-screen">

  <!-- Header -->
  <header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <a href="/trimbook/index.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
      <a href="/trimbook/dashboards/client_dashboard.php" class="text-sm font-medium text-gray-300 hover:text-white transition">← Back to Dashboard</a>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen bg-gradient-to-b from-zinc-950 to-black pt-24 pb-12 px-6">
    <div class="container mx-auto max-w-7xl">
      
      <!-- Page Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-black mb-4">
          Book Your <span class="gradient-text">Appointment</span>
        </h1>
        <p class="text-gray-400 text-lg">Arellano St. Downtown District, Dagupan City</p>
      </div>

      <!-- Two Column Layout -->
      <div class="grid lg:grid-cols-2 gap-8">
        
        <!-- Left Column - Services -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
          <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Select Service
          </h2>
          
          <div class="space-y-3">
            <div class="service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center" onclick="selectService(this)">
              <span class="font-medium">Clean, Sharp and Timeless</span>
              <span class="font-bold">₱350</span>
            </div>
            
            <div class="service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center" onclick="selectService(this)">
              <span class="font-medium">Fade & Line-Up</span>
              <span class="font-bold">₱400</span>
            </div>
            
            <div class="service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center" onclick="selectService(this)">
              <span class="font-medium">Beard Grooming & Design</span>
              <span class="font-bold">₱250</span>
            </div>
            
            <div class="service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center" onclick="selectService(this)">
              <span class="font-medium">Shave & Towel Treatment</span>
              <span class="font-bold">₱300</span>
            </div>
            
            <div class="service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center" onclick="selectService(this)">
              <span class="font-medium">Modern & Trendy Cuts</span>
              <span class="font-bold">₱450</span>
            </div>
            
            <div class="service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center" onclick="selectService(this)">
              <span class="font-medium">Kid's Haircut</span>
              <span class="font-bold">₱200</span>
            </div>
          </div>
        </div>

        <!-- Right Column - Date & Time -->
        <div class="space-y-6">
          
          <!-- Calendar Section -->
          <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              Select Date
            </h2>
            
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
              <button class="w-10 h-10 bg-white/10 rounded-lg hover:bg-white/20 transition" onclick="changeMonth(-1)">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              <span class="text-lg font-semibold" id="currentMonth">October 2024</span>
              <button class="w-10 h-10 bg-white/10 rounded-lg hover:bg-white/20 transition" onclick="changeMonth(1)">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
            
            <!-- Calendar Grid -->
            <div class="bg-white/5 rounded-xl p-4">
              <div class="grid grid-cols-7 gap-2 text-center mb-2">
                <div class="text-xs font-semibold text-gray-400">Sun</div>
                <div class="text-xs font-semibold text-gray-400">Mon</div>
                <div class="text-xs font-semibold text-gray-400">Tue</div>
                <div class="text-xs font-semibold text-gray-400">Wed</div>
                <div class="text-xs font-semibold text-gray-400">Thu</div>
                <div class="text-xs font-semibold text-gray-400">Fri</div>
                <div class="text-xs font-semibold text-gray-400">Sat</div>
              </div>
              
              <div class="grid grid-cols-7 gap-2" id="calendarDays">
                <!-- Calendar days will be generated by JavaScript -->
              </div>
            </div>
          </div>

          <!-- Time Selection -->
          <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-3xl p-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Available Times
            </h2>
            
            <div class="grid grid-cols-3 gap-3">
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">9:00 AM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">9:30 AM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">10:00 AM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">10:30 AM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">11:00 AM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">1:00 PM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">1:30 PM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">2:00 PM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">2:30 PM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">3:00 PM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">3:30 PM</button>
              <button class="time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20" onclick="selectTime(this)">4:00 PM</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirm Button -->
      <div class="text-center mt-12">
        <button id="confirmBtn" disabled
                class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-5 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
          Confirm Appointment
        </button>
        <p id="selectionSummary" class="text-gray-500 text-sm mt-4">Please select service, date, and time</p>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center">
    <p class="text-gray-500 text-sm">&copy; 2024 TrimBook. All Rights Reserved.</p>
  </footer>

  <script>
    let selectedService = null;
    let selectedDate = null;
    let selectedTime = null;

    function selectService(card) {
      document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      selectedService = card.querySelector('span').textContent;
      updateConfirmButton();
    }

    function selectTime(btn) {
      document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      selectedTime = btn.textContent;
      updateConfirmButton();
    }

    function selectDay(day) {
      if (day.classList.contains('disabled')) return;
      document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
      day.classList.add('selected');
      selectedDate = day.textContent;
      updateConfirmButton();
    }

    function updateConfirmButton() {
      const confirmBtn = document.getElementById('confirmBtn');
      const summary = document.getElementById('selectionSummary');
      
      if (selectedService && selectedDate && selectedTime) {
        confirmBtn.disabled = false;
        summary.textContent = `${selectedService} on October ${selectedDate}, 2024 at ${selectedTime}`;
        summary.classList.remove('text-gray-500');
        summary.classList.add('text-blue-400');
      } else {
        confirmBtn.disabled = true;
        const missing = [];
        if (!selectedService) missing.push('service');
        if (!selectedDate) missing.push('date');
        if (!selectedTime) missing.push('time');
        summary.textContent = `Please select ${missing.join(', ')}`;
        summary.classList.add('text-gray-500');
        summary.classList.remove('text-blue-400');
      }
    }

    // Generate calendar
    function generateCalendar() {
      const calendarDays = document.getElementById('calendarDays');
      const today = new Date();
      const daysInMonth = 31;
      const firstDay = 2; // October 1, 2024 is a Tuesday
      
      // Empty cells before first day
      for (let i = 0; i < firstDay; i++) {
        calendarDays.innerHTML += '<div class="h-10"></div>';
      }
      
      // Days of month
      for (let day = 1; day <= daysInMonth; day++) {
        const isPast = day < today.getDate();
        const dayClass = isPast ? 'calendar-day disabled' : 'calendar-day';
        calendarDays.innerHTML += `
          <div class="${dayClass} h-10 flex items-center justify-center rounded-lg text-sm font-medium" 
               onclick="selectDay(this)">
            ${day}
          </div>
        `;
      }
    }

    let currentMonth = 0;
    function changeMonth(direction) {
      currentMonth += direction;
      // In a real app, regenerate calendar for new month
      console.log('Month changed:', currentMonth);
    }

    document.getElementById('confirmBtn').addEventListener('click', function() {
      if (selectedService && selectedDate && selectedTime) {
        alert(`Appointment Confirmed!\n\nService: ${selectedService}\nDate: October ${selectedDate}, 2024\nTime: ${selectedTime}`);
        // Redirect or submit form
      }
    });

    // Initialize calendar on load
    generateCalendar();
  </script>

</body>
</html>
