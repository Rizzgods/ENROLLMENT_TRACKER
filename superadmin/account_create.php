<?php
// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once __DIR__ . "/database.php";

// Check for success/error messages
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message
}
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Account Management</h1>
    
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $message; ?></span>
        </div>
    <?php endif; ?>
    
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-bold mb-4">Create New Account</h2>
        
        <!-- Account Creation Form -->
        <form id="accountForm" action="accr_be.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="account_id" id="account_id" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1" for="accountName">Full Name</label>
                    <input type="text" id="accountName" name="accountName" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1" for="username">Username</label>
                    <input type="text" id="username" name="username" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1" for="accountType">Account Type</label>
                    <select id="accountType" name="accountType" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Account Type</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Registrar">Registrar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1" for="employeeId">Employee ID</label>
                    <input type="text" id="employeeId" name="employeeId" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1" for="password">Password</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <small class="text-gray-500" id="passwordHint">Leave blank to keep current password when editing.</small>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded" id="submitBtn">
                    Create Account
                </button>
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded hidden" id="cancelBtn">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    
    <!-- Admin Table -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">Admin Accounts</h2>
        <input type="text" id="AdminSearch" class="border p-2 w-full mb-4" placeholder="Search Admins by Name...">
        <table id="AdminTable" class="data-table w-full border-collapse border border-gray-300">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Username</th>
                    <th class="border p-2">Role</th>
                    <th class="border p-2">EMPID</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $mydb->query("SELECT ACCOUNT_ID, ACCOUNT_NAME, ACCOUNT_USERNAME, ACCOUNT_TYPE, EMPID FROM useraccounts");
                while ($row = $query->fetch_assoc()) {
                    echo "<tr class='border'>
                            <td class='border p-2'>{$row['ACCOUNT_ID']}</td>
                            <td class='border p-2'>{$row['ACCOUNT_NAME']}</td>
                            <td class='border p-2'>{$row['ACCOUNT_USERNAME']}</td>
                            <td class='border p-2'>{$row['ACCOUNT_TYPE']}</td>
                            <td class='border p-2'>{$row['EMPID']}</td>
                            <td class='border p-2'>
                                <button class='bg-yellow-500 text-white px-2 py-1 rounded edit-btn' data-id='{$row['ACCOUNT_ID']}'>Edit</button>
                                <button class='bg-red-500 text-white px-2 py-1 rounded delete-btn' data-id='{$row['ACCOUNT_ID']}'>Delete</button>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="scripts_js/acc_cr.js"></script>