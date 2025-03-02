<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Change to 0 to prevent errors from breaking JSON
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/create.log');
session_start();

// Set JSON header early to avoid any output before it
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once __DIR__ . "/database.php";

// Handle GET requests (fetch account details)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Account ID is required']);
        exit();
    }

    $id = $mydb->real_escape_string($_GET['id']);
    $query = $mydb->query("SELECT ACCOUNT_ID, ACCOUNT_NAME, ACCOUNT_USERNAME, ACCOUNT_TYPE, EMPID FROM useraccounts WHERE ACCOUNT_ID = '$id'");
    
    if ($query && $query->num_rows > 0) {
        $account = $query->fetch_assoc();
        echo json_encode(['success' => true, 'account' => $account]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
    }
    exit();
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'create':
            createAccount($mydb);
            break;
        case 'update':
            updateAccount($mydb);
            break;
        case 'delete':
            deleteAccount($mydb);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit();
    }
}

/**
 * Create a new account
 */
function createAccount($mydb) {
    try {
        // Validate input
        $required = ['accountName', 'username', 'accountType', 'employeeId', 'password'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                exit();
            }
        }
        
        // Sanitize input
        $accountName = $mydb->real_escape_string($_POST['accountName']);
        $username = $mydb->real_escape_string($_POST['username']);
        $accountType = $mydb->real_escape_string($_POST['accountType']);
        $employeeId = $mydb->real_escape_string($_POST['employeeId']);
        $password = $_POST['password']; // Will be hashed
        $hashedPassword = sha1($password); // Using sha1 for compatibility with existing login system
        
        // Check if username exists
        $checkQuery = $mydb->query("SELECT ACCOUNT_ID FROM useraccounts WHERE ACCOUNT_USERNAME = '$username'");
        if ($checkQuery && $checkQuery->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit();
        }
        
        // Insert new account
        $query = $mydb->query("
            INSERT INTO useraccounts 
            (ACCOUNT_NAME, ACCOUNT_USERNAME, ACCOUNT_PASSWORD, ACCOUNT_TYPE, EMPID, USERIMAGE) 
            VALUES 
            ('$accountName', '$username', '$hashedPassword', '$accountType', '$employeeId', '')
        ");
        
        if ($query) {
            // Log the action
            $admin_id = $_SESSION['id'] ?? 0;
            $logQuery = $mydb->query("INSERT INTO tbllogs (USERID, LOGDATETIME, LOGROLE, LOGMODE) 
                VALUES ('$admin_id', NOW(), 'Superadmin', 'Created account for $accountName')");
            
            $_SESSION['message'] = 'Account created successfully!';
            
            echo json_encode([
                'success' => true, 
                'message' => 'Account created successfully', 
                'redirect' => 'index.php?account=created'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account: ' . $mydb->error]);
        }
    } catch (Exception $e) {
        // Log the error
        error_log("Error creating account: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
    }
    exit();
}

/**
 * Update an existing account
 */
function updateAccount($mydb) {
    try {
        // Validate input
        if (!isset($_POST['account_id']) || empty($_POST['account_id'])) {
            echo json_encode(['success' => false, 'message' => 'Account ID is required']);
            exit();
        }
        
        $required = ['accountName', 'username', 'accountType', 'employeeId'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                exit();
            }
        }
        
        // Sanitize input
        $accountId = $mydb->real_escape_string($_POST['account_id']);
        $accountName = $mydb->real_escape_string($_POST['accountName']);
        $username = $mydb->real_escape_string($_POST['username']);
        $accountType = $mydb->real_escape_string($_POST['accountType']);
        $employeeId = $mydb->real_escape_string($_POST['employeeId']);
        
        // Check if username exists for another account
        $checkQuery = $mydb->query("
            SELECT ACCOUNT_ID FROM useraccounts 
            WHERE ACCOUNT_USERNAME = '$username' AND ACCOUNT_ID != '$accountId'
        ");
        
        if ($checkQuery && $checkQuery->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit();
        }
        
        // Update account
        $sql = "
            UPDATE useraccounts SET 
            ACCOUNT_NAME = '$accountName', 
            ACCOUNT_USERNAME = '$username', 
            ACCOUNT_TYPE = '$accountType', 
            EMPID = '$employeeId'";
        
        // Update password if provided
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $password = $_POST['password'];
            $hashedPassword = sha1($password); // Using sha1 for consistency
            $sql .= ", ACCOUNT_PASSWORD = '$hashedPassword'";
        }
        
        $sql .= " WHERE ACCOUNT_ID = '$accountId'";
        
        $query = $mydb->query($sql);
        
        if ($query) {
            // Log the action
            $admin_id = $_SESSION['id'] ?? 0;
            $logQuery = $mydb->query("INSERT INTO tbllogs (USERID, LOGDATETIME, LOGROLE, LOGMODE) 
                VALUES ('$admin_id', NOW(), 'Superadmin', 'Updated account for $accountName')");
                
            $_SESSION['message'] = 'Account updated successfully!';
            
            echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update account: ' . $mydb->error]);
        }
    } catch (Exception $e) {
        // Log the error
        error_log("Error updating account: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
    }
    exit();
}

/**
 * Delete an account
 */
function deleteAccount($mydb) {
    try {
        // Validate input
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Account ID is required']);
            exit();
        }
        
        $accountId = $mydb->real_escape_string($_POST['id']);
        
        // Get account name for logging
        $nameQuery = $mydb->query("SELECT ACCOUNT_NAME FROM useraccounts WHERE ACCOUNT_ID = '$accountId'");
        $accountName = ($nameQuery && $nameQuery->num_rows > 0) ? $nameQuery->fetch_assoc()['ACCOUNT_NAME'] : 'Unknown';
        
        // Delete account
        $query = $mydb->query("DELETE FROM useraccounts WHERE ACCOUNT_ID = '$accountId'");
        
        if ($query) {
            // Log the action
            $admin_id = $_SESSION['id'] ?? 0;
            $logQuery = $mydb->query("INSERT INTO tbllogs (USERID, LOGDATETIME, LOGROLE, LOGMODE) 
                VALUES ('$admin_id', NOW(), 'Superadmin', 'Deleted account: $accountName')");
                
            echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete account: ' . $mydb->error]);
        }
    } catch (Exception $e) {
        // Log the error
        error_log("Error deleting account: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
    }
    exit();
}
?>