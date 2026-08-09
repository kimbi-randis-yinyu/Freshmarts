        </div>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('hamburgerBtn');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('adminOverlay');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    }

    function openSidebar() {
        if (sidebar) sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        document.body.classList.add('sidebar-open');
    }

    if (hamburger && sidebar) {
        hamburger.addEventListener('click', function () {
            if (sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar when a nav link is clicked (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('nav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 900) closeSidebar();
            });
        });
    }
});
</script>
</body>
</html>
