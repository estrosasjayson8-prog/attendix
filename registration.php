<?php 
// 1. Logic and Database Setup
include 'config.php';
date_default_timezone_set('Asia/Manila');

$alertScript = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fixed: Match the name attributes from your form
    $employeeId = mysqli_real_escape_string($conn, $_POST['employee_id'] ?? '');
    $fullName = mysqli_real_escape_string($conn, $_POST['full_name'] ?? '');

    if (!empty($employeeId) && !empty($fullName)) {
        // Check if ID already exists
        $checkQuery = $conn->query("SELECT * FROM employees WHERE employee_id = '$employeeId'");
        
        if ($checkQuery->num_rows > 0) {
            $alertScript = "Swal.fire({title: 'Error', text: 'Employee ID already registered!', icon: 'error'});";
        } else {
            // Fixed: Removed the extra comma after full_name and corrected variable names
            $sql = "INSERT INTO employees (employee_id, full_name) VALUES ('$employeeId', '$fullName')";
            
            if ($conn->query($sql)) {
                $alertScript = "Swal.fire({title: 'Success!', text: 'Registration Successful!', icon: 'success'}).then(() => { window.location.href = 'index.php'; });";
            } else {
                $alertScript = "Swal.fire({title: 'Error', text: 'Database error: " . $conn->error . "', icon: 'error'});";
            }
        }
    } else {
        $alertScript = "Swal.fire({title: 'Warning', text: 'Please fill in all fields.', icon: 'warning'});";
    }
}

// Use the includes to keep the design consistent
include 'src/includes/header.php'; 
?>

<main class="flex-grow flex flex-col items-center justify-center w-full px-4 max-w-[500px]">
    <div class="adaptive-card shadow-2xl flex flex-col gap-6">
        <div class="text-center">
            <p class="text-gray-400 uppercase text-[0.7rem] tracking-widest font-bold">New Employee</p>
            <h2 class="theme-text text-3xl font-black">REGISTRATION</h2>
        </div>

        <form method="POST" class="flex flex-col gap-4">
            <input type="text" name="employee_id" required placeholder="Employee ID" class="input-field">
            <input type="text" name="full_name" required placeholder="Full Name" class="input-field">
            
            <button type="submit" class="btn-submit shadow-lg mt-2">Register Employee</button>
        </form>
        
        <div class="text-center">
            <a href="index.php" class="theme-text text-xs font-bold uppercase tracking-widest hover:underline">Back to Kiosk</a>
        </div>
    </div>
</main>

<script>
    // This will trigger the SweetAlert from the PHP logic above
    <?php echo $alertScript; ?>
</script>

<?php include 'src/includes/footer.php'; ?>