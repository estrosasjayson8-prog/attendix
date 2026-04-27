<script>
    function applyTheme(theme) {
        const icon = document.getElementById("theme-icon");
        const navBoxes = document.querySelectorAll('.nav-box');
        if (theme === 'night') {
            document.documentElement.setAttribute('data-theme', 'night');
            if (icon) icon.textContent = "☀️";
            navBoxes.forEach(box => box.style.backgroundColor = "#0f172a");
        } else {
            document.documentElement.removeAttribute('data-theme');
            if (icon) icon.textContent = "🌙";
            navBoxes.forEach(box => box.style.backgroundColor = "#800000");
        }
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') === 'night' ? 'day' : 'night';
        applyTheme(currentTheme);
        localStorage.setItem('theme', currentTheme);
    }

    function updateClock() {
        const now = new Date();
        const timeEl = document.getElementById("time");
        const dateEl = document.getElementById("date");
        if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-GB');
        if (dateEl) dateEl.textContent = now.toLocaleDateString('en-GB');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'day';
        applyTheme(savedTheme);
        updateClock();
        setInterval(updateClock, 1000);
    });
  </script>
</body>
</html>