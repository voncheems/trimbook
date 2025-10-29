<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /trimbook/pages/login.php");
    exit;
}

// Check if user has correct role
if ($_SESSION['user_type'] !== 'customer') {
    header('Location: ../unauthorized.php');
    exit;
}

$customer_user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'Guest';

// Get barber_id from URL parameter
$barber_id = isset($_GET['barber_id']) ? intval($_GET['barber_id']) : null;

if (!$barber_id) {
    header("Location: /trimbook/dashboards/client_selectBarber.php");
    exit;
}

// Fetch barber details
$barber_name = 'Unknown Barber';
$db_error = null;

try {
    $conn = new mysqli("localhost", "root", "", "trimbookdb");
    
    if ($conn->connect_error) {
        $db_error = "Database connection failed: " . $conn->connect_error;
    } else {
        // Get barber name
        $barber_query = "
            SELECT u.first_name, u.last_name 
            FROM barbers b
            JOIN users u ON b.user_id = u.user_id
            WHERE b.barber_id = ?
        ";
        
        $stmt = $conn->prepare($barber_query);
        
        if (!$stmt) {
            $db_error = "Query preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("i", $barber_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $barber_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
            } else {
                $db_error = "Barber not found. Please select a valid barber.";
            }
            
            $stmt->close();
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    $db_error = "Error: " . $e->getMessage();
}
?>
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

    .time-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .time-btn.selected {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-color: #667eea;
    }

    .time-btn:disabled {
      cursor: not-allowed;
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

    .loading {
      display: none;
    }

    .loading.show {
      display: inline-block;
    }
  </style>
</head>
<body class="bg-black text-white antialiased min-h-screen">

  <!-- Header -->
  <header class="fixed w-full top-0 left-0 z-50 bg-black/80 backdrop-blur-lg border-b border-gray-800">
    <nav class="container mx-auto flex justify-between items-center py-5 px-6">
      <a href="/trimbook/index.php" class="text-2xl font-black tracking-tight">TRIMBOOK</a>
      <a href="/trimbook/dashboards/client_selectBarber.php" class="text-sm font-medium text-gray-300 hover:text-white transition">← Back</a>
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
        <p class="text-gray-400 text-lg">with <span class="text-blue-400 font-semibold"><?= htmlspecialchars($barber_name) ?></span></p>
      </div>

      <!-- Error Message -->
      <?php if ($db_error): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl">
          <p><?= htmlspecialchars($db_error) ?></p>
        </div>
      <?php endif; ?>

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
          
          <div class="space-y-3" id="servicesContainer">
            <!-- Services will be loaded here -->
            <p class="text-gray-400">Loading services...</p>
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
              <span class="text-lg font-semibold" id="currentMonth"></span>
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
            
            <div class="grid grid-cols-3 gap-3" id="timesContainer">
              <!-- Times will be loaded here -->
              <p class="text-gray-400">Select a date first</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirm Button -->
      <div class="text-center mt-12">
        <button id="confirmBtn" disabled
                class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-5 rounded-full text-lg font-bold hover:shadow-xl hover:shadow-purple-500/50 transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
          <span class="loading" id="loadingSpinner">
            <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          <span id="confirmBtnText">Confirm Appointment</span>
        </button>
        <p id="selectionSummary" class="text-gray-500 text-sm mt-4">Please select service, date, and time</p>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-zinc-950 border-t border-gray-800 py-8 text-center">
    <p class="text-gray-500 text-sm">&copy; <?= date("Y") ?> TrimBook. All Rights Reserved.</p>
  </footer>

  <script>
    const barber_id = <?= $barber_id ?>;
    let selectedService = null;
    let selectedServiceId = null;
    let selectedDate = null;
    let selectedTime = null;
    let currentMonth = new Date();

    // Load services from database
    function loadServices() {
      fetch('../auth/get_service.php')
        .then(response => response.json())
        .then(data => {
          if (data.success && data.services.length > 0) {
            const container = document.getElementById('servicesContainer');
            container.innerHTML = '';
            data.services.forEach(service => {
              const card = document.createElement('div');
              card.className = 'service-card bg-white/10 border border-gray-700 rounded-xl px-5 py-4 flex justify-between items-center';
              card.innerHTML = `
                <span class="font-medium">${service.service_name}</span>
                <span class="font-bold">₱${parseFloat(service.price).toFixed(2)}</span>
              `;
              card.onclick = () => selectService(card, service.service_id, service.service_name);
              container.appendChild(card);
            });
          }
        })
        .catch(err => console.error('Error loading services:', err));
    }

    // Load available times with booked slots disabled
    function loadTimes(dateToFetch = null) {
      const container = document.getElementById('timesContainer');
      container.innerHTML = '';
      
      const times = [
        '9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM', '11:00 AM',
        '1:00 PM', '1:30 PM', '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM', '4:00 PM',
        '4:30 PM', '5:00 PM', '6:00 PM'
      ];
      
      // If a date is provided, fetch booked slots for that date
      if (dateToFetch) {
        fetch('../auth/get_booked_slots.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            barber_id: barber_id,
            appointment_date: dateToFetch
          })
        })
        .then(response => response.json())
        .then(data => {
          const bookedTimes = data.success ? data.booked_times : [];
          
          times.forEach(time => {
            const btn = document.createElement('button');
            const isBooked = bookedTimes.includes(time);
            
            if (isBooked) {
              btn.className = 'time-btn bg-red-500/20 border border-red-500/30 rounded-lg px-4 py-3 font-medium text-red-400 cursor-not-allowed opacity-60';
              btn.textContent = time + ' (Booked)';
              btn.disabled = true;
            } else {
              btn.className = 'time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20';
              btn.textContent = time;
              btn.onclick = () => selectTime(btn, time);
            }
            
            container.appendChild(btn);
          });
        })
        .catch(err => {
          console.error('Error loading booked slots:', err);
          // Fallback: show all times if fetch fails
          times.forEach(time => {
            const btn = document.createElement('button');
            btn.className = 'time-btn bg-white/10 border border-gray-700 rounded-lg px-4 py-3 font-medium hover:bg-white/20';
            btn.textContent = time;
            btn.onclick = () => selectTime(btn, time);
            container.appendChild(btn);
          });
        });
      } else {
        // No date selected yet
        const p = document.createElement('p');
        p.className = 'text-gray-400 col-span-3';
        p.textContent = 'Select a date first';
        container.appendChild(p);
      }
    }

    function selectService(card, serviceId, serviceName) {
      document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      selectedService = serviceName;
      selectedServiceId = serviceId;
      updateConfirmButton();
    }

    function selectTime(btn, time) {
      document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      selectedTime = time;
      updateConfirmButton();
    }

    function selectDay(day) {
      if (day.classList.contains('disabled')) return;
      document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
      day.classList.add('selected');
      selectedDate = day.dataset.date;
      
      // Load times for this specific date (will show booked slots as disabled)
      loadTimes(selectedDate);
      
      // Clear time selection when date changes
      selectedTime = null;
      updateConfirmButton();
    }

    function updateConfirmButton() {
      const confirmBtn = document.getElementById('confirmBtn');
      const summary = document.getElementById('selectionSummary');
      
      if (selectedService && selectedDate && selectedTime) {
        confirmBtn.disabled = false;
        const dateObj = new Date(selectedDate);
        const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        summary.textContent = `${selectedService} on ${formattedDate} at ${selectedTime}`;
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

    function generateCalendar() {
      const calendarDays = document.getElementById('calendarDays');
      const monthYear = document.getElementById('currentMonth');
      
      const year = currentMonth.getFullYear();
      const month = currentMonth.getMonth();
      
      monthYear.textContent = currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
      
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      
      // Get current date in Philippine Time (UTC+8)
      const now = new Date();
      const phTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
      const today = new Date(phTime.getFullYear(), phTime.getMonth(), phTime.getDate());
      
      calendarDays.innerHTML = '';
      
      // Empty cells
      for (let i = 0; i < firstDay; i++) {
        calendarDays.innerHTML += '<div class="h-10"></div>';
      }
      
      // Days
      for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const isPast = date < today;
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayClass = isPast ? 'calendar-day disabled' : 'calendar-day';
        
        calendarDays.innerHTML += `
          <div class="${dayClass} h-10 flex items-center justify-center rounded-lg text-sm font-medium" 
               data-date="${dateStr}"
               onclick="selectDay(this)">
            ${day}
          </div>
        `;
      }
    }

    function changeMonth(direction) {
      currentMonth.setMonth(currentMonth.getMonth() + direction);
      generateCalendar();
    }

    document.getElementById('confirmBtn').addEventListener('click', function() {
      if (selectedService && selectedDate && selectedTime && selectedServiceId) {
        const btn = this;
        const spinner = document.getElementById('loadingSpinner');
        const btnText = document.getElementById('confirmBtnText');
        
        btn.disabled = true;
        spinner.classList.add('show');
        btnText.textContent = 'Booking...';
        
        // Convert time to 24-hour format
        const time24 = convertTo24Hour(selectedTime);
        
        const payload = {
          barber_id: barber_id,
          service_id: selectedServiceId,
          appointment_date: selectedDate,
          appointment_time: time24
        };
        
        console.log('Sending payload:', payload);
        
        fetch('../auth/create_appointment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
          spinner.classList.remove('show');
          
          if (data.success) {
            btnText.textContent = 'Appointment Booked!';
            setTimeout(() => {
              window.location.href = '/trimbook/dashboards/client_dashboard.php';
            }, 2000);
          } else {
            alert('Error: ' + data.message);
            btnText.textContent = 'Confirm Appointment';
            btn.disabled = false;
          }
        })
        .catch(err => {
          console.error('Error:', err);
          alert('An error occurred. Please try again.');
          spinner.classList.remove('show');
          btnText.textContent = 'Confirm Appointment';
          btn.disabled = false;
        });
      }
    });

    function convertTo24Hour(time12) {
      const [time, period] = time12.split(' ');
      let [hours, minutes] = time.split(':');
      hours = parseInt(hours);
      
      if (period === 'PM' && hours !== 12) {
        hours += 12;
      } else if (period === 'AM' && hours === 12) {
        hours = 0;
      }
      
      return `${String(hours).padStart(2, '0')}:${minutes}:00`;
    }

    // Initialize on page load
    generateCalendar();
    loadServices();
    loadTimes();
  </script>

</body>
</html>
