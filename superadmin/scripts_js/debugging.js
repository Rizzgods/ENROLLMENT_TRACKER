// Add this file to your page to debug navigation issues
document.addEventListener('DOMContentLoaded', function() {
    console.log("=== DEBUGGING SIDEBAR NAVIGATION ===");
    
    // Check for all necessary elements
    const elements = {
        'home-btn': document.getElementById('home-btn'),
        'account-btn': document.getElementById('account-btn'),
        'logs-btn': document.getElementById('logs-btn'),
        'list-content': document.getElementById('list-content'),
        'account-content': document.getElementById('account-content'),
        'logs-content': document.getElementById('logs-content')
    };
    
    console.table(Object.entries(elements).map(([id, element]) => ({
        id: id,
        exists: element !== null,
        classes: element ? element.className : 'N/A'
    })));
    
    // Add manual navigation helpers
    console.log("=== MANUAL NAVIGATION HELPERS ===");
    console.log("Run these commands to test navigation:");
    console.log("showHome() - Show home content");
    console.log("showAccount() - Show account content");
    console.log("showLogs() - Show logs content");
    
    window.showHome = function() {
        if (elements['list-content']) elements['list-content'].classList.remove('hidden');
        if (elements['account-content']) elements['account-content'].classList.add('hidden');
        if (elements['logs-content']) elements['logs-content'].classList.add('hidden');
        console.log("Showing home content");
    };
    
    window.showAccount = function() {
        if (elements['list-content']) elements['list-content'].classList.add('hidden');
        if (elements['account-content']) elements['account-content'].classList.remove('hidden');
        if (elements['logs-content']) elements['logs-content'].classList.add('hidden');
        console.log("Showing account content");
    };
    
    window.showLogs = function() {
        if (elements['list-content']) elements['list-content'].classList.add('hidden');
        if (elements['account-content']) elements['account-content'].classList.add('hidden');
        if (elements['logs-content']) elements['logs-content'].classList.remove('hidden');
        console.log("Showing logs content");
    };
    
    // Add click event listeners directly to test buttons
    document.querySelectorAll('[id$="-btn"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            console.log(`Button ${this.id} clicked`);
        });
    });
});
