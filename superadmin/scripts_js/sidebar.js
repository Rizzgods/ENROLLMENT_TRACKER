// Simple, direct sidebar navigation script
window.addEventListener('load', function() {
    console.log('=== SIDEBAR NAVIGATION LOADED ===');
    
    // Direct DOM element references
    const homeBtn = document.getElementById('home-btn');
    const accountBtn = document.getElementById('account-btn');
    const logsBtn = document.getElementById('logs-btn');
    
    const listContent = document.getElementById('list-content');
    const accountContent = document.getElementById('account-content');
    const logsContent = document.getElementById('logs-content');
    
    console.log('Elements found:', {
        homeBtn: !!homeBtn,
        accountBtn: !!accountBtn,
        logsBtn: !!logsBtn,
        listContent: !!listContent,
        accountContent: !!accountContent,
        logsContent: !!logsContent
    });
    
    // Direct click handlers with minimal logic
    if (homeBtn) {
        homeBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Home clicked');
            
            // Show/hide content
            listContent.style.display = 'block';
            accountContent.style.display = 'none';
            logsContent.style.display = 'none';
            
            // Update styles
            homeBtn.className = 'bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
            accountBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
            logsBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
        };
    }
    
    if (accountBtn) {
        accountBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Account clicked');
            
            // Show/hide content
            listContent.style.display = 'none';
            accountContent.style.display = 'block';
            logsContent.style.display = 'none';
            
            // Update styles
            homeBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
            accountBtn.className = 'bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
            logsBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
        };
    }
    
    if (logsBtn) {
        logsBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Logs clicked');
            
            // Show/hide content
            listContent.style.display = 'none';
            accountContent.style.display = 'none';
            logsContent.style.display = 'block';
            
            // Update styles
            homeBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
            accountBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
            logsBtn.className = 'bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
        };
    }
    
    // Explicitly set default view
    if (homeBtn) {
        console.log('Setting default view');
        // Trigger home view
        listContent.style.display = 'block';
        accountContent.style.display = 'none';
        logsContent.style.display = 'none';
        
        // Update styles
        homeBtn.className = 'bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
        accountBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
        logsBtn.className = 'bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
        console.log('Default view set');
    }
});