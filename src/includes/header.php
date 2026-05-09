<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendix - Management</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('attendix-theme');
            if (savedTheme === 'night') {
                document.documentElement.setAttribute('data-theme', 'night');
            }
        })();
    </script>

    <style>
        /* 1. LOCK PAGE JUMPING */
        html { overflow-y: scroll; scroll-behavior: smooth; }

        :root {
            --bg-body: #800000; --bg-card: #f5f2f0;
            --text-brand: #800000; --text-hero: #f5f2f0;
            --border-color: rgba(0,0,0,0.1); --nav-bg: #800000;
        }

        [data-theme='night'], [data-theme='night'] body {
            --bg-body: #0f172a; --bg-card: #1e293b;
            --text-brand: #f5f2f0; --text-hero: #f5f2f0;
            --border-color: rgba(255,255,255,0.1); --nav-bg: #0f172a;
        }

        body { 
            background-color: var(--bg-body) !important; 
            color: var(--text-brand);
            transition: background-color 0.3s ease;
            margin: 0;
            width: 100%;
            overflow-x: hidden;
        }

        nav { background-color: var(--nav-bg) !important; transition: 0.3s; }
        
        .theme-badge {
            background-color: var(--bg-card) !important;
            color: var(--text-brand) !important;
            border: 1px solid var(--border-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 4px 12px;
            /* Using a system-level monospace stack for zero-latency loading */
            font-family: ui-monospace, 'Cascadia Code', 'Source Code Pro', Menlo, Consolas, monospace !important;
            font-variant-numeric: tabular-nums;
        }

        /* THE FIX: Fixed width containers for individual parts */
        .time-box { width: 85px; text-align: center; display: inline-block; }
        .ampm-box { width: 25px; text-align: center; display: inline-block; font-size: 10px; opacity: 0.8; }
        #date { min-width: 85px; text-align: center; display: inline-block; }

        .hero-title { color: var(--text-hero) !important; }
    </style>
</head>

<body>
    <nav class="w-full flex justify-between items-center px-4 sm:px-8 py-3 shadow-md fixed top-0 z-50 h-16">
        <a href="home.php" class="hero-title font-mono text-[20px] uppercase tracking-[0.3em]">
            ATTENDIX
        </a>
        
        <div class="flex items-center gap-2">
            <button onclick="toggleTheme()" class="theme-badge rounded-md text-xs cursor-pointer">
                <span id="theme-icon">🌙</span>
            </button>
            
            <div class="theme-badge rounded-md text-xs">
                <span id="time-part" class="time-box">00:00:00</span>
                <span id="ampm-part" class="ampm-box">--</span>
            </div>
            
            <div class="theme-badge rounded-md text-xs">
                <span id="date">00/00/0000</span>
            </div>
        </div>
    </nav>

    <div class="h-24"></div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            if (html.getAttribute('data-theme') === 'night') {
                html.removeAttribute('data-theme');
                if(icon) icon.innerText = '🌙';
                localStorage.setItem('attendix-theme', 'maroon');
            } else {
                html.setAttribute('data-theme', 'night');
                if(icon) icon.innerText = '☀️';
                localStorage.setItem('attendix-theme', 'night');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const timeEl = document.getElementById('time-part');
            const ampmEl = document.getElementById('ampm-part');
            const dateEl = document.getElementById('date');

            function updateClock() {
                if (!timeEl || !dateEl) return;
                const now = new Date();
                
                // Get full string
                const fullStr = now.toLocaleTimeString('en-US', { 
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
                });

                // Split into "12:00:00" and "PM"
                const parts = fullStr.split(' ');
                const timeStr = parts[0];
                const ampmStr = parts[1];
                
                const dateStr = now.toLocaleDateString('en-US', {
                    month: '2-digit', day: '2-digit', year: 'numeric'
                });

                // Update only on change
                if (timeEl.textContent !== timeStr) timeEl.textContent = timeStr;
                if (ampmEl.textContent !== ampmStr) ampmEl.textContent = ampmStr;
                if (dateEl.textContent !== dateStr) dateEl.textContent = dateStr;
            }

            updateClock();
            setInterval(updateClock, 500);
        });
    </script>
</body>
</html>