<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials from Logic_enroll.php
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

echo "<h1>Testing Database Connection</h1>";

// Test connection
try {
    echo "<p>Attempting to connect to database...</p>";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>");
    }
    
    echo "<p style='color:green'>Connection successful!</p>";
    
    // Check if tblstudent table exists
    $result = $conn->query("SHOW TABLES LIKE 'tblstudent'");
    if ($result->num_rows > 0) {
        echo "<p style='color:green'>tblstudent table exists.</p>";
        
        // Count records
        $count = $conn->query("SELECT COUNT(*) as total FROM tblstudent");
        $row = $count->fetch_assoc();
        echo "<p>Total student records: " . $row['total'] . "</p>";
        
        // Show most recent 5 students
        echo "<h2>Recent Student Records:</h2>";
        $students = $conn->query("SELECT IDNO, FNAME, LNAME, EMAIL FROM tblstudent ORDER BY IDNO DESC LIMIT 5");
        
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        
        while ($student = $students->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $student['IDNO'] . "</td>";
            echo "<td>" . $student['FNAME'] . " " . $student['LNAME'] . "</td>";
            echo "<td>" . $student['EMAIL'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
    } else {
        echo "<p style='color:red'>tblstudent table does not exist!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
