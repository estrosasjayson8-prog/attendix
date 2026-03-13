<?php
// index.php - Attendix Employee Attendance

// Logical handling for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId = $_POST['employee_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!empty($employeeId)) {
        // Here you would typically insert into your database
        // For now, we'll just show a simple alert
        echo "<script>alert('Employee $employeeId performed a $action at " . date("H:i") . "');</script>";
    } else {
        echo "<script>alert('Please enter an Employee ID');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendix - Employee Attendance</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#800000] min-h-screen font-sans">

  <nav class="bg-[#f5f2f0] w-full flex justify-between items-center px-8 py-3 shadow-md fixed top-0 z-50 h-16">
    <h1 class="text-[#800000] font-bold text-xl tracking-wider">ATTENDIX</h1>
    <div class="flex gap-3">
      <div class="bg-[#800000] text-white px-4 py-1 rounded-md text-sm font-mono">
        <span id="time">00:00:00</span>
      </div>
      <div class="bg-[#800000] text-white px-4 py-1 rounded-md text-sm">
        <span id="date">--/--/----</span>
      </div>
    </div>
  </nav>

  <div class="h-24"></div>

  <main class="flex flex-col items-center px-4">
    
    <form method="POST" action="" class="flex flex-col items-center gap-8 w-full max-w-sm">
      
      <div class="w-full">
        <input
          type="text"
          name="employee_id"
          required
          placeholder="Enter Employee ID"
          class="w-full border-2 border-transparent rounded-lg px-4 py-3 text-center text-lg shadow-inner focus:outline-none focus:border-yellow-500 transition-all"
          autocomplete="off"
        >
      </div>

      <div class="bg-[#f5f2f0] w-full aspect-[4/5] rounded-3xl shadow-2xl p-10 flex flex-col justify-center items-center gap-6">
        <div class="text-center mb-4">
            <p class="text-gray-600 uppercase text-xs tracking-widest font-bold">Department Name</p>
            <h2 class="text-[#800000] text-2xl font-black">ATTENDANCE</h2>
        </div>

        <button type="submit" name="action" value="Time In" 
          class="bg-[#800000] text-white py-4 rounded-xl shadow-lg hover:bg-green-700 hover:-translate-y-1 active:scale-95 transition-all w-full font-bold text-lg uppercase">
          Time In
        </button>
        
        <button type="submit" name="action" value="Time Out" 
          class="bg-gray-800 text-white py-4 rounded-xl shadow-lg hover:bg-red-700 hover:-translate-y-1 active:scale-95 transition-all w-full font-bold text-lg uppercase">
          Time Out
        </button>
      </div>
      
    </form>
  </main>

  <script>
    function updateClock() {
      const now = new Date();
      // Added seconds for a more "live" feel
      const time = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      const date = now.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
      
      document.getElementById("time").textContent = time;
      document.getElementById("date").textContent = date;
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>

</body>
</html>