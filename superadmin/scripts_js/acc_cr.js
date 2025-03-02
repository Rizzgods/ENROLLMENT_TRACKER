document.addEventListener('DOMContentLoaded', function() {
    const accountForm = document.getElementById('accountForm');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    
    // Add form submission handler
    if (accountForm) {
        accountForm.addEventListener('submit', function(e) {
            e.preventDefault();  // Prevent regular form submission
            
            const formData = new FormData(this);
            
            fetch('accr_be.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    
                    // Check if redirect is specified
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
        });
    }
    
    // Search functionality
    const adminSearch = document.getElementById('AdminSearch');
    if (adminSearch) {
        adminSearch.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#AdminTable tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const username = row.cells[2].textContent.toLowerCase();
                if (name.includes(searchValue) || username.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Edit button functionality
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const accountId = this.getAttribute('data-id');
            fetchAccountDetails(accountId);
        });
    });
    
    // Delete button functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const accountId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this account?')) {
                deleteAccount(accountId);
            }
        });
    });
    
    // Cancel button functionality
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            resetForm();
        });
    }
    
    // Function to fetch account details for editing
    function fetchAccountDetails(accountId) {
        // Show loading state or spinner if needed
        
        // Send AJAX request to get account details
        fetch(`accr_be.php?action=get&id=${accountId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Scroll to the top of the page
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth' // Optional smooth scrolling
                    });
                    
                    // Populate the form with account details
                    document.getElementById('account_id').value = data.account.ACCOUNT_ID;
                    document.getElementById('accountName').value = data.account.ACCOUNT_NAME;
                    document.getElementById('username').value = data.account.ACCOUNT_USERNAME;
                    document.getElementById('accountType').value = data.account.ACCOUNT_TYPE;
                    document.getElementById('employeeId').value = data.account.EMPID;
                    
                    // Change form action to update
                    accountForm.querySelector('input[name="action"]').value = 'update';
                    
                    // Change button text
                    submitBtn.textContent = 'Update Account';
                    
                    // Show cancel button
                    cancelBtn.classList.remove('hidden');
                    
                    // Password hint
                    document.getElementById('passwordHint').classList.remove('hidden');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching account details.');
            });
    }
    
    // Function to delete an account
    function deleteAccount(accountId) {
        // Send AJAX request to delete account
        fetch('accr_be.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${accountId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Account deleted successfully!');
                // Reload the page to refresh the table
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the account.');
        });
    }
    
    // Function to reset the form
    function resetForm() {
        // Reset form fields
        accountForm.reset();
        
        // Reset hidden fields
        document.getElementById('account_id').value = '';
        accountForm.querySelector('input[name="action"]').value = 'create';
        
        // Reset button text
        submitBtn.textContent = 'Create Account';
        
        // Hide cancel button
        cancelBtn.classList.add('hidden');
        
        // Hide password hint
        document.getElementById('passwordHint').classList.add('hidden');
    }
});