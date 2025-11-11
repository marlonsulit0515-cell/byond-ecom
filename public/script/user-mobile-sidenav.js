document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            const overlay = document.getElementById('overlay');

            if (!sidebar || !sidebarToggle || !closeSidebar || !overlay) {
                console.error('Sidebar elements not found');
                return;
            }

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                // document.body.style.overflow = 'hidden'; 
            }

            function closeSidebarMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                // document.body.style.overflow = '';
            }

            // Event listeners
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                openSidebar();
            });

            closeSidebar.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebarMenu();
            });

            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebarMenu();
            });

            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                    closeSidebarMenu();
                }
            });

            // Close sidebar when clicking on navigation links (mobile only)
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeSidebarMenu();
                    }
                });
            });
        });