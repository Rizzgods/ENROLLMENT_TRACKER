document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    let isSidebarOpen = false;

    function toggleSidebar() {
        isSidebarOpen = !isSidebarOpen;
        if (isSidebarOpen) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
            sidebarToggle.classList.add('opacity-0');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
            sidebarToggle.classList.remove('opacity-0');
        }
    }

    // Toggle sidebar on button click
    sidebarToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleSidebar();
    });

    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', (e) => {
        e.stopPropagation();
        if (isSidebarOpen) toggleSidebar();
    });

    // Prevent sidebar from closing when clicking inside it
    sidebar.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Handle responsive behavior
    function handleResize() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
            sidebarToggle.classList.remove('opacity-0');
        } else {
            if (!isSidebarOpen) {
                sidebar.classList.add('-translate-x-full');
                sidebarToggle.classList.remove('opacity-0');
            }
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check

    // Remove the fetch event listeners since we're using direct page loads
    document.getElementById('documents-info').addEventListener('click', function(e) {
        // Let the normal link navigation happen
        return true;
    });

    // Update active state based on current page
    function updateActiveState() {
        const currentPage = new URLSearchParams(window.location.search).get('page') || 'profile';
        document.querySelectorAll('nav a').forEach(link => {
            const linkPage = new URLSearchParams(link.href.split('?')[1]).get('page');
            if (linkPage === currentPage) {
                link.classList.add('text-blue-200');
            } else {
                link.classList.remove('text-blue-200');
            }
        });
    }

    updateActiveState();
});