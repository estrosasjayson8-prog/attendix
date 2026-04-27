<?php 
// 1. Logic and Database Setup (Must be at the very top)
include 'config.php';
date_default_timezone_set('Asia/Manila');

$alertScript = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId = mysqli_real_escape_string($conn, $_POST['employee_id'] ?? '');
    $action = $_POST['action'] ?? '';
    if (!empty($employeeId) && !empty($action)) {
        $userQuery = $conn->query("SELECT full_name FROM employees WHERE employee_id = '$employeeId'");
        if ($userQuery->num_rows == 0) {
            $alertScript = "Swal.fire({title: 'Error', text: 'ID Not Found. Please register first.', icon: 'error'});";
        } else {
            $user = $userQuery->fetch_assoc();
            $name = $user['full_name'];
            if ($action == "Time In") {
                $check = $conn->query("SELECT * FROM logs WHERE employee_id = '$employeeId' AND time_out IS NULL");
                if ($check->num_rows > 0) {
                    $alertScript = "Swal.fire({title: 'Warning', text: 'Hi $name, you are already Timed In!', icon: 'warning'});";
                } else {
                    $conn->query("INSERT INTO logs (employee_id, time_in) VALUES ('$employeeId', NOW())");
                    $alertScript = "Swal.fire({title: 'Success!', text: 'Welcome, $name! In at " . date("h:i A") . "', icon: 'success'});";
                }
            } elseif ($action == "Time Out") {
                $check = $conn->query("SELECT * FROM logs WHERE employee_id = '$employeeId' AND time_out IS NULL ORDER BY id DESC LIMIT 1");
                if ($check->num_rows == 0) {
                    $alertScript = "Swal.fire({title: 'Error', text: 'Hi $name, no active session found.', icon: 'error'});";
                } else {
                    $row = $check->fetch_assoc();
                    $conn->query("UPDATE logs SET time_out = NOW() WHERE id = " . $row['id']);
                    $alertScript = "Swal.fire({title: 'Success!', text: 'Goodbye, $name! Out at " . date("h:i A") . "', icon: 'success'});";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendix - Kiosk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root { --bg-main: #800000; --bg-card: #f5f2f0; --text-accent: #800000; --btn-in: #800000; --btn-out: #1f2937; }
    [data-theme='night'] { --bg-main: #06012b; --bg-card: #1e293b; --text-accent: #38bdf8; --btn-in: #334155; --btn-out: #0f172a; }
    body { background-color: var(--bg-main); transition: background-color 0.5s ease; overflow-x: hidden; }
    .adaptive-card { background-color: var(--bg-card); width: 100%; max-width: 500px; padding: clamp(1.5rem, 5vw, 2.5rem); border-radius: 2rem; }
    .theme-text { color: var(--text-accent); transition: color 0.5s ease; }
    .btn-action { padding: 1.25rem; transition: all 0.3s ease; border-radius: 1rem; }
    .btn-time-in { background-color: var(--btn-in); }
    .btn-time-out { background-color: var(--btn-out); }
    .nav-box { transition: background-color 0.5s ease; }
  </style>
</head>
<body class="min-h-screen flex flex-col items-center">
  <nav class="bg-[#f5f2f0] w-full flex justify-between items-center px-4 sm:px-8 py-3 shadow-md fixed top-0 z-50 h-16">
    <h1 ondblclick="window.location.href='src/includes/login.php'" 
    class="theme-text text-4xl font-black uppercase cursor-default select-none">
    ATTENDIX
</h1>
    <div class="flex items-center gap-2">
      <button id="theme-toggle" onclick="toggleTheme()" class="bg-[#800000] text-white w-9 h-8 rounded-md flex items-center justify-center nav-box hover:scale-105"><span id="theme-icon">🌙</span></button>
      <div class="bg-[#800000] text-white px-2 sm:px-4 py-1 rounded-md text-[10px] sm:text-xs font-mono nav-box"><span id="time">00:00:00</span></div>
      <div class="bg-[#800000] text-white px-2 sm:px-4 py-1 rounded-md text-[10px] sm:text-xs nav-box"><span id="date">--/--/----</span></div>
    </div>
  </nav>

  <div class="h-24"></div>

  <main class="flex-grow flex flex-col items-center justify-center w-full px-4 max-w-[500px] gap-6">
    <form method="POST" class="w-full flex flex-col gap-6">
      <input type="text" name="employee_id" required autofocus placeholder="Enter Employee ID" class="w-full border-2 border-transparent rounded-2xl px-5 py-4 text-center text-xl bg-white shadow-xl focus:border-yellow-500 outline-none transition-all">
      <div class="adaptive-card shadow-2xl flex flex-col items-center gap-8">
        <div class="text-center">
            <p class="text-gray-400 uppercase text-[0.7rem] tracking-widest font-bold">Punch In/Out</p>
            <h2 class="theme-text text-3xl font-black">ATTENDANCE</h2>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 w-full">
          <button type="submit" name="action" value="Time In" class="btn-action btn-time-in text-white shadow-lg w-full font-bold uppercase active:scale-95">Time In</button>
          <button type="submit" name="action" value="Time Out" class="btn-action btn-time-out text-white shadow-lg w-full font-bold uppercase active:scale-95">Time Out</button>
        </div>
      </div>
    </form>
    <div class="flex flex-col items-center gap-2">
       
       
  </main>

  <script>
    // Trigger SweetAlerts
    <?php echo $alertScript; ?>

    // Theme Logic
    function applyTheme(theme) {
        const icon = document.getElementById("theme-icon");
        const navBoxes = document.querySelectorAll('.nav-box');
        if (theme === 'night') {
            document.documentElement.setAttribute('data-theme', 'night');
            icon.textContent = "☀️";
            navBoxes.forEach(box => box.style.backgroundColor = "#000000");
        } else {
            document.documentElement.removeAttribute('data-theme');
            icon.textContent = "🌙";
            navBoxes.forEach(box => box.style.backgroundColor = "#800000");
        }
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') === 'night' ? 'day' : 'night';
        applyTheme(currentTheme);
        localStorage.setItem('theme', currentTheme);
    }

    // Initialize Theme on load
    const savedTheme = localStorage.getItem('theme') || 'day';
    applyTheme(savedTheme);

    // Clock Logic
    function updateClock() {
        const now = new Date();
        document.getElementById("time").textContent = now.toLocaleTimeString('en-GB');
        document.getElementById("date").textContent = now.toLocaleDateString('en-GB');
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>

<?php include 'src/includes/footer.php'; ?>