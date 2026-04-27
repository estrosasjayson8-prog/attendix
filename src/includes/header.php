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
        :root {
            --bg-body: #800000;
            --bg-card: #f5f2f0;
            --text-brand: #800000;
            --text-hero: #f5f2f0;
            --border-color: rgba(0,0,0,0.1);
            --nav-bg: #800000;
        }

        /* Target HTML or Body for the theme */
        [data-theme='night'], [data-theme='night'] body {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-brand: #f5f2f0;
            --text-hero: #f5f2f0;
            --border-color: rgba(255,255,255,0.1);
            --nav-bg: #0f172a;
        }

        body { 
            background-color: var(--bg-body) !important; 
            color: var(--text-brand);
            transition: background-color 0.3s ease;
            margin: 0;
        }

        /* Navbar & Badges */
        nav { background-color: var(--nav-bg) !important; transition: 0.3s; }
        
        .theme-badge {
            background-color: var(--bg-card) !important;
            color: var(--text-brand) !important;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .adaptive-card { background-color: var(--bg-card) !important; }
        .adaptive-text { color: var(--text-brand) !important; }
        .hero-title { color: var(--text-hero) !important; }
    </style>
</head>

<body>
    <nav class="w-full flex justify-between items-center px-4 sm:px-8 py-3 shadow-md fixed top-0 z-50 h-16">
        <h1 class="hero-title font-mono text-[20px] uppercase tracking-[0.3em]">ATTENDIX</h1>
        
        <div class="flex items-center gap-2">
            <button onclick="toggleTheme()" class="theme-badge px-4 py-1 rounded-md text-xs font-mono">
                <span id="theme-icon">🌙</span>
            </button>
            <div class="theme-badge px-4 py-1 rounded-md text-xs font-mono">
                <span id="time">00:00:00</span>
            </div>
            <div class="theme-badge px-4 py-1 rounded-md text-xs font-mono">
                <span id="date">--/--/----</span>
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
                icon.innerText = '🌙';
                localStorage.setItem('attendix-theme', 'maroon');
            } else {
                html.setAttribute('data-theme', 'night');
                icon.innerText = '☀️';
                localStorage.setItem('attendix-theme', 'night');
            }
        }

        // Set icon correctly on load without flickering
        window.addEventListener('DOMContentLoaded', () => {
            const icon = document.getElementById('theme-icon');
            if (document.documentElement.getAttribute('data-theme') === 'night') {
                icon.innerText = '☀️';
            }
            
            // Clock Logic
            setInterval(() => {
                const now = new Date();
                document.getElementById('time').innerText = now.toLocaleTimeString();
                document.getElementById('date').innerText = now.toLocaleDateString();
            }, 1000);
        });
    </script>