<?php
class AttendanceModel {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    // 1. Fetch recent logs for the dashboard monitor
    public function getRecentLogs() {
        $sql = "SELECT l.*, e.full_name FROM logs l 
                JOIN employees e ON l.employee_id = e.employee_id 
                ORDER BY l.id DESC LIMIT 10";
        return $this->db->query($sql);
    }

    // 2. Fetch all employees for the management table
    public function getAllEmployees() {
        return $this->db->query("SELECT * FROM employees ORDER BY full_name ASC");
    }

    // 3. Update basic employee information
    public function updateEmployee($id, $name, $dept, $position) {
        $sql = "UPDATE employees SET full_name = ?, department = ?, position = ? WHERE employee_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $name, $dept, $position, $id);
        return $stmt->execute();
    }

    // 4. Update ONLY the avatar path
    public function updateAvatar($employee_id, $filename) {
        $stmt = $this->db->prepare("UPDATE employees SET avatar = ? WHERE employee_id = ?");
        $stmt->bind_param("ss", $filename, $employee_id);
        return $stmt->execute();
    }

    // 5. Delete an employee
    public function deleteEmployee($id) {
        $stmt = $this->db->prepare("DELETE FROM employees WHERE employee_id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    // 6. Register a new employee
    public function registerEmployee($id, $name, $dept, $position) {
        $sql = "INSERT INTO employees (employee_id, full_name, department, position) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $id, $name, $dept, $position);
        return $stmt->execute();
    }

    public function getAttendanceStats() {
    $date = date('Y-m-d');
    $stats = [];
    
    // Total employees
    $res = $this->db->query("SELECT COUNT(*) as total FROM employees");
    $stats['total'] = $res->fetch_assoc()['total'];
    
    // Present Today (unique employees logged today)
    $res = $this->db->query("SELECT COUNT(DISTINCT employee_id) as present FROM logs WHERE DATE(time_in) = '$date'");
    $stats['present'] = $res->fetch_assoc()['present'];
    
    return $stats;
}

public function getActiveEmployees() {
    // Returns IDs of employees who have a 'Time In' today but no 'Time Out' yet
    $date = date('Y-m-d');
    $ids = [];
    $res = $this->db->query("SELECT employee_id FROM logs WHERE DATE(time_in) = '$date' AND (time_out IS NULL OR time_out = '')");
    while($row = $res->fetch_assoc()) { $ids[] = $row['employee_id']; }
    return $ids;
}
}
?>