<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Redirect back to the main Kiosk (index.php)
// Since logout.php is in src/includes/, we go up two levels
header("Location: ../../index.php");
exit();