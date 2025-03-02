<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgreenvalley";

// Create connection
$mydb = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);

// Check connection
if ($mydb->connect_error) {
    die("Connection failed: " . $mydb->connect_error);
}

$admin_count = $mydb->query("SELECT COUNT(*) AS count FROM useraccounts")->fetch_assoc()['count'];
$pre_enrollees_count = $mydb->query("SELECT COUNT(*) AS count FROM `tblstudent` s, course c WHERE s.COURSE_ID=c.COURSE_ID AND NewEnrollees=1 AND student_status='New'")->fetch_assoc()['count'];
$enrollees_count = $mydb->query("SELECT COUNT(*) AS count FROM tblstudent s
	JOIN course c ON s.COURSE_ID = c.COURSE_ID
	LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
	WHERE sa.STATUS = 'accepted'
	AND NOT EXISTS (
	SELECT 1 FROM student st WHERE st.id = s.IDNO
)")->fetch_assoc()['count'];
$students_count = $mydb->query("SELECT COUNT(*) AS count FROM tblstudent s
	JOIN course c ON s.COURSE_ID = c.COURSE_ID
	LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
	INNER JOIN student st ON s.IDNO = st.id")->fetch_assoc()['count'];


?>



