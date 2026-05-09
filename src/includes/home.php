<?php 
session_start();

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit();
}

include '../../config.php';
include '../../model/AttendanceModel.php';

$attendance = new AttendanceModel($conn);

// --- HANDLE POST ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['avatar_file'])) {
        $emp_id = $_POST['target_employee_id'];
        $target_dir = "../../public/assets/uploads/avatars/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $file_ext = pathinfo($_FILES["avatar_file"]["name"], PATHINFO_EXTENSION);
        $new_filename = "avatar_" . $emp_id . "_" . time() . "." . $file_ext;
        if (move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target_dir . $new_filename)) {
            $attendance->updateAvatar($emp_id, $new_filename);
            header("Location: home.php?status=photo_updated");
            exit();
        }
    }
    
    if (isset($_POST['register_new'])) {
        $new_id = $_POST['employee_id'];
        $new_name = $_POST['full_name'];
        $new_dept = $_POST['department'];
        $new_pos = $_POST['position']; 
        if (!ctype_digit($new_id) || preg_match("/[0-9]/", $new_name) || preg_match("/[0-9]/", $new_pos)) {
            header("Location: home.php?status=error_invalid_input");
            exit();
        }
        $attendance->registerEmployee($new_id, $new_name, $new_dept, $new_pos);
        header("Location: home.php?status=registered");
        exit();
    }

    if (isset($_POST['update_info'])) {
        $id = $_POST['id'];
        $name = $_POST['full_name'];
        $dept = $_POST['department'];
        $pos = $_POST['position'];
        if (preg_match("/[0-9]/", $name) || preg_match("/[0-9]/", $pos)) {
            header("Location: home.php?status=error_invalid_input");
            exit();
        }
        $attendance->updateEmployee($id, $name, $dept, $pos);
        header("Location: home.php?status=updated");
        exit();
    }
}

$employees = $attendance->getAllEmployees();
$activeIds = (method_exists($attendance, 'getActiveEmployees')) ? $attendance->getActiveEmployees() : [];
include 'header.php'; 
?>

<style>
    /* SYNC WITH HEADER THEME */
    [data-theme='night'] body {
        --brand-primary: #f5f2f0; /* Cream for Night Mode buttons */
        --brand-text: #0f172a;    /* Navy text */
    }

    body {
        --brand-primary: #800000; /* Maroon for Light Mode buttons */
        --brand-text: #ffffff;    /* White text */
    }

    /* Keep UI elements consistent with the theme variables from header.php */
    .primary-btn { 
        background-color: var(--brand-primary) !important; 
        color: var(--brand-text) !important;
        transition: all 0.3s ease;
    }

    .adaptive-text { color: var(--text-brand) !important; }
    .adaptive-card { background-color: var(--bg-card) !important; border: 1px solid var(--border-color); }
    
    /* Avatar Button styling */
    .avatar-label {
        background-color: var(--bg-card) !important;
        color: var(--text-brand) !important;
        border: 1px solid var(--border-color) !important;
        cursor: pointer;
    }
    .avatar-label:hover {
        background-color: var(--text-brand) !important;
        color: var(--bg-card) !important;
    }

    /* Input styling for modals & search */
    .modal-input { 
        background: var(--bg-card) !important;
        color: var(--text-brand) !important;
        border: 1px solid var(--border-color) !important;
        @apply w-full p-4 rounded-2xl outline-none transition-all text-sm font-bold; 
    }

    /* Terminate button styling */
    .terminate-btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: var(--text-hero);
    }
</style>

<main class="w-full max-w-7xl mx-auto py-12 px-6">
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="hero-title text-6xl font-black uppercase tracking-tighter leading-none">ADMIN FORTE</h1>
            <p class="hero-title opacity-60 font-mono text-[10px] uppercase tracking-[0.3em] mt-2">Enterprise Resource Management </p>
        </div>
        <a href="logout.php" class="terminate-btn px-6 py-2.5 rounded-xl font-bold text-[10px] uppercase hover:bg-white/20 transition-all shadow-sm">
            Back to KIOSK
        </a>
    </div>

    <div class="flex flex-col md:flex-row items-center gap-4 mb-10">
        <div class="adaptive-card p-4 rounded-2xl w-full md:w-48 shadow-sm flex flex-col justify-center min-h-[85px]">
            <p class="label-caps !mb-0 text-[8px] opacity-50 uppercase font-black">Total Workforce</p>
            <h3 class="adaptive-text text-2xl font-black"><?php echo $employees->num_rows; ?></h3>
        </div>

        <div class="adaptive-card p-4 rounded-2xl w-full md:w-48 shadow-sm flex flex-col justify-center min-h-[85px]">
            <p class="label-caps !mb-0 text-[8px] opacity-50 uppercase font-black">Active Personnel</p>
            <h3 class="adaptive-text text-2xl font-black"><?php echo count($activeIds); ?></h3>
        </div>

        <div class="relative w-full md:max-w-xs ml-auto">
            <input type="text" id="employeeSearch" placeholder="Search directory..." 
                   class="modal-input !py-4 !px-5 !rounded-xl shadow-sm pr-12 text-[11px] w-full">
            <div class="absolute right-4 top-1/2 -translate-y-1/2 adaptive-text opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <section class="adaptive-card rounded-[2.5rem] shadow-2xl overflow-hidden mb-12">
        <div class="p-8 border-b border-black/5 flex justify-between items-center bg-black/5">
            <h2 class="adaptive-text font-black text-xl uppercase tracking-tight">Employee Directory</h2>
           <div class="flex gap-3">
    <a href="attendance_report.php" class="adaptive-card border-black/10 px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-wider shadow-sm flex items-center hover:bg-black/5 transition-all">
        📊 Attendance Logs
    </a>
    
    <button onclick="openDeleteModal()"  class="primary-btn px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-wider shadow-lg">
        🗑️ Delete Staff
    </a>

    <button onclick="openRegisterModal()" class="primary-btn px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-wider shadow-lg">
        + Register New Entry
    </button>
</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left" id="employeeTable">
                <thead>
                    <tr class="bg-black/5">
                        <th class="py-4 px-8 text-[10px] font-black uppercase opacity-40 tracking-widest adaptive-text">Identity</th>
                        <th class="py-4 px-4 text-[10px] font-black uppercase opacity-40 tracking-widest adaptive-text">Credential</th>
                        <th class="py-4 px-4 text-[10px] font-black uppercase opacity-40 tracking-widest adaptive-text">Position</th>
                        <th class="py-4 px-4 text-[10px] font-black uppercase opacity-40 tracking-widest adaptive-text">Unit</th>
                        <th class="py-4 px-4 text-[10px] font-black uppercase opacity-40 tracking-widest adaptive-text">Presence</th>
                        <th class="py-4 px-8 text-right text-[10px] font-black uppercase opacity-40 tracking-widest adaptive-text">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-black/5">
                    <?php foreach($employees as $emp): 
                        $isLive = in_array($emp['employee_id'], $activeIds);
                    ?>
                    <tr class="hover:bg-black/5 transition-all group">
                        <td class="py-5 px-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl overflow-hidden border adaptive-card flex items-center justify-center">
                                    <?php if(!empty($emp['avatar']) && $emp['avatar'] !== 'default.png'): ?>
                                        <img src="../../public/assets/uploads/avatars/<?php echo $emp['avatar']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="adaptive-text font-black text-lg"><?php echo strtoupper(substr($emp['full_name'] ?? 'U', 0, 1)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-black adaptive-text text-sm"><?php echo htmlspecialchars($emp['full_name']); ?></p>
                                    <p class="text-[9px] uppercase font-bold opacity-40 tracking-wider adaptive-text">Verified Employee</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-5 px-4"><span class="font-mono font-bold text-xs bg-black/5 adaptive-text px-3 py-1 rounded-md">ID: <?php echo $emp['employee_id']; ?></span></td>
                        <td class="py-5 px-4 font-bold adaptive-text italic text-sm opacity-80"><?php echo htmlspecialchars($emp['position'] ?? 'Staff'); ?></td>
                        <td class="py-5 px-4 font-black adaptive-text opacity-60 text-[10px] uppercase"><?php echo htmlspecialchars($emp['department'] ?? 'General'); ?></td>
                        <td class="py-5 px-4">
                            <?php if($isLive): ?>
                                <div class="inline-flex items-center gap-2 bg-green-500/10 text-green-500 px-3 py-1 rounded-xl border border-green-500/20">
                                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                                    <span class="text-[8px] font-black uppercase tracking-tight">Active</span>
                                </div>
                            <?php else: ?>
                                <span class="text-[8px] font-black uppercase opacity-30 adaptive-text px-3 py-1">Offline</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-5 px-8">
                            <div class="flex justify-end items-center gap-2">
                                <form method="POST" enctype="multipart/form-data" class="m-0">
                                    <input type="hidden" name="target_employee_id" value="<?php echo $emp['employee_id']; ?>">
                                    <label class="avatar-label text-[9px] font-black uppercase px-4 py-2 rounded-lg transition-all block">
                                        Avatar
                                        <input type="file" name="avatar_file" class="hidden" onchange="this.form.submit()">
                                    </label>
                                </form>
                                <button onclick='openEditModal(<?php echo json_encode($emp); ?>)' class="primary-btn font-black text-[9px] uppercase px-5 py-2 rounded-lg shadow-md hover:scale-105 transition-all">
                                    Modify
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="registerModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-6">
    <div class="adaptive-card w-full max-w-md rounded-[3rem] p-10 shadow-3xl border-t-[12px] relative" style="border-top-color: var(--brand-primary);">
        <button onclick="closeRegisterModal()" class="absolute top-8 right-8 text-gray-400 hover:adaptive-text font-bold">✕</button>
        <h2 class="text-3xl font-black adaptive-text mb-2 uppercase italic tracking-tighter">New Entry</h2>
        <form method="POST" class="space-y-5">
            <div><label class="label-caps">Employee ID</label><input type="text" name="employee_id" required class="modal-input"></div>
            <div><label class="label-caps">Full Name</label><input type="text" name="full_name" required class="modal-input"></div>
            <div><label class="label-caps">Position</label><input type="text" name="position" required class="modal-input"></div>
            <div><label class="label-caps">Unit</label><input type="text" name="department" required class="modal-input"></div>
            <button type="submit" name="register_new" class="primary-btn w-full py-4 font-black uppercase text-xs rounded-2xl shadow-lg">Confirm Entry</button>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-6">
    <div class="adaptive-card w-full max-w-md rounded-[3rem] p-10 shadow-3xl border-t-[12px] relative" style="border-top-color: var(--brand-primary);">
        <button onclick="closeEditModal()" class="absolute top-8 right-8 text-gray-400 hover:adaptive-text font-bold">✕</button>
        <h2 class="text-3xl font-black adaptive-text mb-2 uppercase italic tracking-tighter">Modify</h2>
        <form method="POST" class="space-y-5">
            <input type="hidden" name="id" id="edit_id">
            <div><label class="label-caps">Full Name</label><input type="text" name="full_name" id="edit_name" required class="modal-input"></div>
            <div><label class="label-caps">Position</label><input type="text" name="position" id="edit_position" required class="modal-input"></div>
            <div><label class="label-caps">Unit</label><input type="text" name="department" id="edit_dept" required class="modal-input"></div>
            <button type="submit" name="update_info" class="primary-btn w-full py-4 font-black uppercase text-xs rounded-2xl shadow-lg">Commit Changes</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-6">
    <div class="adaptive-card w-full max-w-md rounded-[3rem] p-10 shadow-3xl border-t-[12px] border-red-700 relative">
        <button onclick="closeDeleteModal()" class="absolute top-8 right-8 text-gray-400 hover:adaptive-text font-bold">✕</button>
        
        <h2 class="text-3xl font-black text-red-700 mb-2 uppercase italic tracking-tighter">Terminate Record</h2>
        <p class="text-[10px] uppercase font-bold opacity-50 mb-6 adaptive-text">Authorized personal removal only.</p>
        
        <form action="delete_employee.php" method="POST" class="space-y-5">
            <div>
                <label class="label-caps text-[9px]">Select Employee to Remove</label>
                <select name="employee_id" required class="modal-input">
                    <option value="" disabled selected>Select from directory...</option>
                    <?php 
                    // This uses the existing $employees variable already defined in your home.php
                    foreach($employees as $e): 
                    ?>
                        <option value="<?php echo $e['employee_id']; ?>">
                            [<?php echo $e['employee_id']; ?>] <?php echo htmlspecialchars($e['full_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="label-caps text-[9px]">Security Password</label>
                <input type="password" name="admin_password" placeholder="Enter admin123" required class="modal-input">
            </div>

            <button type="submit" name="confirm_delete" class="bg-red-700 text-white w-full py-4 font-black uppercase text-xs rounded-2xl shadow-lg hover:scale-[1.02] transition-all">
                Confirm Deletion
            </button>
        </form>
    </div>
</div>

<script>
function openDeleteModal() { document.getElementById('deleteModal').classList.remove('hidden'); }
function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }
function openRegisterModal() { document.getElementById('registerModal').classList.remove('hidden'); }
function closeRegisterModal() { document.getElementById('registerModal').classList.add('hidden'); }
function openEditModal(emp) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('edit_id').value = emp.employee_id;
    document.getElementById('edit_name').value = emp.full_name;
    document.getElementById('edit_position').value = emp.position;
    document.getElementById('edit_dept').value = emp.department;
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }

window.onclick = function(e) {
    const modals = ['editModal', 'registerModal', 'deleteModal'];
    modals.forEach(id => {
        if (e.target.id === id) {
            document.getElementById(id).classList.add('hidden');
        }
    });
}

document.getElementById('employeeSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelector("#employeeTable tbody").rows;
    for (let i = 0; i < rows.length; i++) {
        let nameCol = rows[i].cells[0].textContent.toUpperCase();
        let idCol = rows[i].cells[1].textContent.toUpperCase();
        rows[i].style.display = (nameCol.indexOf(filter) > -1 || idCol.indexOf(filter) > -1) ? "" : "none";
    }
});
</script>

<?php include 'footer.php'; ?>