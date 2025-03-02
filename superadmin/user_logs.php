<?php
// Remove the reference to the missing dbconfig.php file
// and establish a direct connection instead

// Check if we already have a connection
if (!isset($conn)) {
    // Set up database connection directly
    $conn = new mysqli("localhost", "root", "", "dbgreenvalley");

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
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
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
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['LOGID']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['USER_NAME']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['LOGROLE']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['LOGMODE']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['LOGDATETIME']; ?></td>
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
</div>

<?php
// Only close connection if we opened it in this file
if (!isset($conn_closed) && isset($conn)) {
    $conn->close();
    $conn_closed = true;
}
?>