<?php
// Remove the reference to the missing dbconfig.php file
// and establish a direct connection instead

// Check if we already have a connection
if (!isset($conn)) {
    // Set up database connection directly
    $conn = new mysqli("localhost", "admi_greenvalley", "xr9%kxu%*my^+kf2", "admi_dbgreenvalley");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Query to get user logs from the database
// Updated SQL to use the correct table and column names
$sql = "SELECT l.*, 
        CASE 
            WHEN s.username IS NOT NULL THEN s.username
            WHEN u.ACCOUNT_NAME IS NOT NULL THEN u.ACCOUNT_NAME
            ELSE 'Unknown User' 
        END as USER_NAME
        FROM tbllogs l
        LEFT JOIN studentaccount s ON l.USERID = s.user_id
        LEFT JOIN useraccounts u ON l.USERID = u.ACCOUNT_ID
        ORDER BY l.LOGDATETIME DESC";

$result = $conn->query($sql);

// Debug SQL query results if needed
$error = $conn->error;
?>

<!-- User Logs Content -->
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">User Activity Logs</h2>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p>Error executing query: <?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <!-- Filter Controls -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <h3 class="text-lg font-medium mb-3">Filter Logs</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="user-filter" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <input type="text" id="user-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Filter by username">
            </div>
            <div>
                <label for="role-filter" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">All Roles</option>
                    <option value="administrator">Administrator</option>
                    <option value="student">Student</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>
            <div>
                <label for="activity-filter" class="block text-sm font-medium text-gray-700 mb-1">Activity</label>
                <input type="text" id="activity-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Filter by activity">
            </div>
            <div>
                <label for="date-filter" class="block text-sm font-medium text-gray-700 mb-1">Date (YYYY-MM-DD)</label>
                <input type="date" id="date-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button id="reset-filters" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md mr-3">
                Reset Filters
            </button>
            <button id="apply-filters" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
                Apply Filters
            </button>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table id="logs-table" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="log-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['LOGID']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 user-cell"><?php echo $row['USER_NAME']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 role-cell"><?php echo $row['LOGROLE']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 activity-cell"><?php echo $row['LOGMODE']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 date-cell"><?php echo $row['LOGDATETIME']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No logs found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- No Results Message -->
    <div id="no-results" class="hidden mt-4 p-4 bg-yellow-50 text-yellow-700 rounded-md border border-yellow-300">
        No logs match your filter criteria. Try adjusting your filters.
    </div>
</div>

<!-- JavaScript for filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userFilter = document.getElementById('user-filter');
    const roleFilter = document.getElementById('role-filter');
    const activityFilter = document.getElementById('activity-filter');
    const dateFilter = document.getElementById('date-filter');
    const applyButton = document.getElementById('apply-filters');
    const resetButton = document.getElementById('reset-filters');
    const noResults = document.getElementById('no-results');
    const logRows = document.querySelectorAll('.log-row');
    
    // Apply filters when the button is clicked
    applyButton.addEventListener('click', filterLogs);
    
    // Reset filters when reset button is clicked
    resetButton.addEventListener('click', function() {
        userFilter.value = '';
        roleFilter.value = '';
        activityFilter.value = '';
        dateFilter.value = '';
        
        // Show all rows again
        logRows.forEach(row => {
            row.classList.remove('hidden');
        });
        
        // Hide the no results message if it's showing
        noResults.classList.add('hidden');
    });
    
    // Filter logs based on the input values
    function filterLogs() {
        const userValue = userFilter.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const activityValue = activityFilter.value.toLowerCase();
        const dateValue = dateFilter.value ? dateFilter.value : '';
        
        let visibleCount = 0;
        
        // Loop through all rows and check if they match the filter criteria
        logRows.forEach(row => {
            const user = row.querySelector('.user-cell').textContent.toLowerCase();
            const role = row.querySelector('.role-cell').textContent.toLowerCase();
            const activity = row.querySelector('.activity-cell').textContent.toLowerCase();
            const dateTime = row.querySelector('.date-cell').textContent;
            const date = dateTime.split(' ')[0]; // Extract just the date part
            
            // Check if the row matches all filter criteria
            const userMatch = userValue === '' || user.includes(userValue);
            const roleMatch = roleValue === '' || role === roleValue;
            const activityMatch = activityValue === '' || activity.includes(activityValue);
            const dateMatch = dateValue === '' || date.includes(dateValue);
            
            // Show or hide the row based on the filter match
            if (userMatch && roleMatch && activityMatch && dateMatch) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Show "no results" message if all rows are hidden
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
    
    // Enable filtering on Enter key in text inputs
    [userFilter, activityFilter, dateFilter].forEach(input => {
        input.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                filterLogs();
            }
        });
    });
});
</script>

<?php
// Only close connection if we opened it in this file
if (!isset($conn_closed) && isset($conn)) {
    $conn->close();
    $conn_closed = true;
}
?>