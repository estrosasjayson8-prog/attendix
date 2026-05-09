<?php 
session_start();

// --- AUTHENTICATION CHECK ---
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit();
}

include '../../config.php';
include '../../model/AttendanceModel.php';

$attendance = new AttendanceModel($conn);

// --- LOGIC HANDLING ---
$target_id = $_GET['employee_id'] ?? null;

if ($target_id) {
    // Single Employee View: Fetch specific profile and their log history
    $prof_query = "SELECT * FROM employees WHERE employee_id = ?";
    $p_stmt = $conn->prepare($prof_query);
    $p_stmt->bind_param("s", $target_id);
    $p_stmt->execute();
    $profile = $p_stmt->get_result()->fetch_assoc();

    $query = "SELECT * FROM logs WHERE employee_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $target_id);
} else {
    // Directory View: Show all registered employees
    $query = "SELECT * FROM employees ORDER BY full_name ASC";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

include 'header.php'; 
?>

<style>
    /* SYNC WITH HOME.PHP THEME VARIABLES */
    :root {
        --brand-maroon: #800000;
        --brand-cream: #f5f2f0;
    }

    [data-theme='night'] body {
        --brand-primary: var(--brand-cream); 
        --brand-text: #0f172a; 
    }

    body {
        --brand-primary: var(--brand-maroon);
        --brand-text: #ffffff;
    }

    /* COMPONENT STYLING */
    .primary-btn { 
        background-color: var(--brand-primary) !important; 
        color: var(--brand-text) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .adaptive-card { 
        background-color: var(--bg-card) !important; 
        border: 1px solid var(--border-color); 
        transition: transform 0.2s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    }

    .profile-avatar-container {
        background: linear-gradient(135deg, var(--brand-maroon), #4a0000);
        position: relative;
        overflow: hidden;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .search-input {
        background: var(--bg-card) !important;
        color: var(--text-brand) !important;
        border: 1px solid var(--border-color) !important;
    }

    /* Table Customization */
    .report-table thead th {
        background: rgba(0,0,0,0.02);
        border-bottom: 1px solid var(--border-color);
    }

    .status-badge {
        font-size: 8px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
        letter-spacing: 0.05em;
    }
</style>

<main class="w-full max-w-7xl mx-auto py-12 px-6">
    
    <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
        <div>
            <h1 class="hero-title text-6xl font-black uppercase tracking-tighter leading-none">
                <?php echo $target_id ? "Personnel File" : "Staff Archives"; ?>
            </h1>
            <p class="hero-title opacity-60 font-mono text-[10px] uppercase tracking-[0.4em] mt-3">
                Attendix / Data Visualization / Report Engine
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="home.php" class="adaptive-card px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black/5 transition-all flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return to Forte
            </a>
            <?php if($target_id): ?>
                <a href="attendance_report.php" class="primary-btn px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg">
                    Directory View
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(!$target_id): ?>
    <div class="mb-10 max-w-md">
        <div class="relative">
            <input type="text" id="reportSearch" placeholder="Filter staff by name or ID..." 
                   class="search-input w-full p-5 rounded-2xl outline-none text-sm font-bold shadow-sm pr-12">
            <div class="absolute right-5 top-1/2 -translate-y-1/2 opacity-30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!$target_id): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="reportGrid">
            <?php while($row = $result->fetch_assoc()): ?>
                <a href="?employee_id=<?php echo $row['employee_id']; ?>" 
                   class="adaptive-card glass-card rounded-[3rem] p-10 flex flex-col items-center text-center group">
                    
                    <div class="w-28 h-28 rounded-[2.5rem] profile-avatar-container mb-6 shadow-xl border-4 border-white/10 flex items-center justify-center">
                        <?php if(!empty($row['avatar']) && $row['avatar'] !== 'default.png'): ?>
                            <img src="../../public/assets/uploads/avatars/<?php echo $row['avatar']; ?>" class="avatar-img" alt="Profile">
                        <?php else: ?>
                            <span class="text-3xl font-black text-white"><?php echo strtoupper(substr($row['full_name'] ?? 'E', 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>

                    <h2 class="font-black text-2xl tracking-tighter uppercase mb-1 adaptive-text"><?php echo htmlspecialchars($row['full_name']); ?></h2>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-red-800 mb-8"><?php echo $row['position']; ?></p>
                    
                    <div class="w-full pt-8 border-t border-black/5 flex justify-between items-center">
                        <div class="text-left">
                            <p class="text-[8px] uppercase opacity-40 font-black">ID Serial</p>
                            <p class="font-mono font-bold text-xs adaptive-text"><?php echo $row['employee_id']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] uppercase opacity-40 font-black">Unit</p>
                            <p class="font-bold text-[10px] adaptive-text uppercase"><?php echo $row['department']; ?></p>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <div class="adaptive-card rounded-[3.5rem] p-12 mb-10 shadow-2xl flex flex-col md:flex-row items-center gap-12">
            <div class="w-44 h-44 rounded-[3rem] profile-avatar-container text-6xl font-black shadow-2xl flex items-center justify-center border-[6px] border-white/10">
                <?php if(!empty($profile['avatar']) && $profile['avatar'] !== 'default.png'): ?>
                    <img src="../../public/assets/uploads/avatars/<?php echo $profile['avatar']; ?>" class="avatar-img" alt="Profile">
                <?php else: ?>
                    <span class="text-white"><?php echo strtoupper(substr($profile['full_name'] ?? 'E', 0, 1)); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="text-center md:text-left flex-1">
                <div class="inline-block px-4 py-1 bg-red-800 text-white text-[9px] font-black uppercase tracking-widest rounded-lg mb-4">
                    Official Personnel File
                </div>
                <h1 class="text-5xl font-black uppercase tracking-tighter mb-4 adaptive-text"><?php echo htmlspecialchars($profile['full_name']); ?></h1>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-6">
                    <div>
                        <p class="text-[9px] font-black uppercase opacity-40">Department</p>
                        <p class="font-bold adaptive-text uppercase tracking-tight"><?php echo $profile['department']; ?></p>
                    </div>
                    <div class="w-[1px] bg-black/10 hidden md:block"></div>
                    <div>
                        <p class="text-[9px] font-black uppercase opacity-40">Position</p>
                        <p class="font-bold adaptive-text uppercase tracking-tight"><?php echo $profile['position']; ?></p>
                    </div>
                    <div class="w-[1px] bg-black/10 hidden md:block"></div>
                    <div>
                        <p class="text-[9px] font-black uppercase opacity-40">Employee ID</p>
                        <p class="font-mono font-bold adaptive-text"><?php echo $profile['employee_id']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="adaptive-card rounded-[3rem] overflow-hidden shadow-xl">
            <div class="p-8 bg-black/[0.02] border-b border-black/5 flex justify-between items-center">
                <h3 class="font-black uppercase text-xs tracking-widest adaptive-text">Attendance History</h3>
                <span class="text-[9px] font-bold opacity-40 uppercase">Real-time Log Data</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left report-table">
                    <thead>
                        <tr>
                            <th class="py-6 px-10 text-[10px] font-black uppercase tracking-widest opacity-50 adaptive-text">Date</th>
                            <th class="py-6 px-6 text-[10px] font-black uppercase tracking-widest opacity-50 adaptive-text">Clock In</th>
                            <th class="py-6 px-6 text-[10px] font-black uppercase tracking-widest opacity-50 adaptive-text">Clock Out</th>
                            <th class="py-6 px-10 text-right text-[10px] font-black uppercase tracking-widest opacity-50 adaptive-text">Log Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-black/5">
                        <?php if($result->num_rows > 0): ?>
                            <?php while($log = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-black/[0.01] transition-colors">
                                    <td class="py-6 px-10">
                                        <p class="font-black text-sm adaptive-text"><?php echo date('M d, Y', strtotime($log['created_at'])); ?></p>
                                        <p class="text-[9px] opacity-40 font-bold uppercase"><?php echo date('l', strtotime($log['created_at'])); ?></p>
                                    </td>
                                    <td class="py-6 px-6 font-mono font-black text-green-600 text-sm">
                                        <?php echo date('h:i:s A', strtotime($log['time_in'])); ?>
                                    </td>
                                    <td class="py-6 px-6 font-mono font-black text-red-600 text-sm">
                                        <?php echo $log['time_out'] ? date('h:i:s A', strtotime($log['time_out'])) : '--- : ---'; ?>
                                    </td>
                                    <td class="py-6 px-10 text-right">
                                        <?php if($log['time_out']): ?>
                                            <span class="status-badge bg-black/5 adaptive-text border border-black/10">Archived</span>
                                        <?php else: ?>
                                            <span class="status-badge bg-green-500/10 text-green-600 border border-green-500/20">Session Active</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-20 text-center">
                                    <p class="text-[10px] font-black uppercase opacity-20 tracking-[0.5em]">No activity logs found for this period</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</main>

<script>
// SEARCH FILTER LOGIC (Matching home.php style)
document.getElementById('reportSearch')?.addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let cards = document.querySelectorAll("#reportGrid > a");
    
    cards.forEach(card => {
        let name = card.querySelector('h2').textContent.toUpperCase();
        let id = card.querySelector('.font-mono').textContent.toUpperCase();
        
        if (name.indexOf(filter) > -1 || id.indexOf(filter) > -1) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    });
});
</script>

<?php include 'footer.php'; ?>