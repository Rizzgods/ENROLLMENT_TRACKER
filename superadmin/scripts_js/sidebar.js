document.addEventListener('DOMContentLoaded', function() {
    // Get content sections
    const listContent = document.getElementById('list-content');
    const accountContent = document.getElementById('account-content');
    
    // Get sidebar buttons
    const homeBtn = document.getElementById('home-btn');
    const accountBtn = document.getElementById('account-btn');
    
    // Function to show list content
    homeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show list content, hide account content
        listContent.classList.remove('hidden');
        accountContent.classList.add('hidden');
        
        // Update active state
        homeBtn.classList.add('bg-white', 'text-blue-700');
        homeBtn.classList.remove('bg-blue-700', 'text-white');
        
        accountBtn.classList.remove('bg-white', 'text-blue-700');
        accountBtn.classList.add('bg-blue-700', 'text-white');
    });
    
    // Function to show account content
    accountBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show account content, hide list content
        accountContent.classList.remove('hidden');
        listContent.classList.add('hidden');
        
        // Update active state
        accountBtn.classList.add('bg-white', 'text-blue-700');
        accountBtn.classList.remove('bg-blue-700', 'text-white');
        
        homeBtn.classList.remove('bg-white', 'text-blue-700');
        homeBtn.classList.add('bg-blue-700', 'text-white');
    });
    
    // Set home as default active button
    homeBtn.click();
});