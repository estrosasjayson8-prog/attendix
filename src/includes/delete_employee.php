<?php
session_start();

// 1. Security Check: Ensure only logged-in admins can access this script
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit();
}

include '../../config.php';
include '../../model/AttendanceModel.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    $emp_id = $_POST['employee_id'];
    $password = $_POST['admin_password'];

    // 2. Password Condition
    if ($password === 'admin123') {
        $attendance = new AttendanceModel($conn);
        
        // 3. Database Cleanup
        // First, delete logs associated with the employee to avoid foreign key errors
        $stmt_logs = $conn->prepare("DELETE FROM logs WHERE employee_id = ?");
        $stmt_logs->bind_param("s", $emp_id);
        $stmt_logs->execute();
        
        // Then delete the employee record
        $stmt_emp = $conn->prepare("DELETE FROM employees WHERE employee_id = ?");
        $stmt_emp->bind_param("s", $emp_id);
        
        if ($stmt_emp->execute()) {
            header("Location: home.php?status=deleted");
        } else {
            header("Location: home.php?status=error_delete_failed");
        }
        exit();
    } else {
        // Redirect back if password is wrong
        header("Location: home.php?status=error_wrong_password");
        exit();
    }
} else {
    // If someone tries to access the file directly without POST
    header("Location: home.php");
    exit();
}