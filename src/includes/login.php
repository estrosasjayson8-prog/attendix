<?php
session_start();
if(isset($_SESSION['admin_auth'])) { header("Location: home.php"); exit(); }

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['admin_password'];
    
    // Set your secret password here
    if ($password === 'admin123') { 
        $_SESSION['admin_auth'] = true;
        header("Location: home.php");
        exit();
    } else {
        $error = "Access Denied: Invalid Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Attendix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Gradient using your hex codes */
        body { 
            background: linear-gradient(135deg, #CF0A4F 0%, #D16BA5 50%, #EC1A1A 100%);
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(245, 242, 240, 0.95); /* Cream base with slight transparency */
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-sm glass-card p-10 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] border-t-8 border-[#800000]">
        
        <div class="text-center mb-10">
            <h2 class="text-[#800000] text-3xl font-black uppercase tracking-tighter italic">Forte</h2>
            <div class="h-1 w-12 bg-[#800000] mx-auto my-2 rounded-full"></div>
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.2em]">Management Access</p>
        </div>

        <form method="POST" class="space-y-6">
            <?php if($error): ?>
                <div class="animate-bounce">
                    <p class="text-red-600 text-[10px] font-black uppercase text-center bg-red-50 p-3 rounded-xl border border-red-100 shadow-sm">
                        ⚠️ <?php echo $error; ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="relative group">
                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest block mb-2 ml-1">
                    System Access Key
                </label>
                <input type="password" name="admin_password" autofocus required 
                       placeholder="••••••••"
                       class="w-full p-5 rounded-2xl bg-white border-2 border-gray-100 text-[#800000] font-mono tracking-[0.5em] text-center shadow-inner focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 outline-none transition-all duration-300">
                
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-[#800000] rounded-full transition-all duration-500 group-focus-within:w-1/2 opacity-50"></div>
            </div>

            <button type="submit" 
                    class="w-full py-5 bg-[#800000] text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#600000] hover:-translate-y-1 active:scale-95 transition-all duration-300">
                Authenticate
            </button>
            
            <div class="pt-4 border-t border-gray-200">
                <a href="../../index.php" class="flex items-center justify-center gap-2 text-gray-400 text-[10px] font-black uppercase hover:text-[#800000] transition-colors group">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span> 
                    Return to Kiosk
                </a>
            </div>
        </form>
    </div>
</body>
</html>