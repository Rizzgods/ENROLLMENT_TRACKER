<?php
include('database.php'); // Ensure this file connects to your database

// Fetch courses with student count
$courseQuery = "
    SELECT c.COURSE_NAME, COUNT(s.IDNO) AS student_count
    FROM tblstudent s
    JOIN course c ON s.COURSE_ID = c.COURSE_ID
    INNER JOIN student st ON s.IDNO = st.id  -- Ensures only students in 'student' table are included
    WHERE s.student_status = 'approved'
    GROUP BY c.COURSE_NAME
";

$courseResult = $mydb->query($courseQuery);

$courseNames = [];
$courseCounts = [];

while ($row = $courseResult->fetch_assoc()) {
    $courseNames[] = $row['COURSE_NAME'];
    $courseCounts[] = (int) $row['student_count'];
}

// Fetch enrollees for the current week
$weekQuery = "
    SELECT COUNT(user_id) AS enroll_count
    FROM studentaccount
    WHERE STATUS = 'accepted'
    AND YEARWEEK(enrollment_date, 1) = YEARWEEK(CURDATE(), 1)  -- Only current week's data
";

$weekResult = $mydb->query($weekQuery);

$weekLabel = "Week " . date("W"); // Current week number
$enrollCount = 0;

if ($row = $weekResult->fetch_assoc()) {
    $enrollCount = (int) $row['enroll_count'];
}

// Fetch accepted vs total applications
$statusQuery = "
    SELECT 
        SUM(CASE WHEN STATUS = 'accepted' THEN 1 ELSE 0 END) AS accepted_count,
        SUM(CASE WHEN STATUS IN ('accepted', 'rejected') THEN 1 ELSE 0 END) AS total_count
    FROM studentaccount
";

$statusResult = $mydb->query($statusQuery);

$acceptedCount = 0;
$totalCount = 0;

if ($row = $statusResult->fetch_assoc()) {
    $acceptedCount = (int) $row['accepted_count'];
    $totalCount = (int) $row['total_count'];
}

// Fetch enrollments for today
$dayQuery = "
    SELECT COUNT(user_id) AS enroll_count
    FROM studentaccount
    WHERE STATUS = 'accepted'
    AND DATE(enrollment_date) = CURDATE()  -- Only today's enrollments
";

$dayResult = $mydb->query($dayQuery);

$dayLabel = date("F j, Y"); // Example: "March 27, 2025"
$dailyCount = 0; // ✅ Changed from $enrollCount to $dailyCount

if ($row = $dayResult->fetch_assoc()) {
    $dailyCount = (int) $row['enroll_count'];
}

// Calculate accepted percentage
$acceptedPercentage = $totalCount > 0 ? round(($acceptedCount / $totalCount) * 100, 2) : 0;

// Fetch payment statistics
$paymentQuery = "
    SELECT 
        SUM(CASE WHEN PAYMENT = 'paid' THEN 1 ELSE 0 END) AS paid_count,
        SUM(CASE WHEN PAYMENT = 'unpaid' THEN 1 ELSE 0 END) AS unpaid_count
    FROM studentaccount
";

$paymentResult = $mydb->query($paymentQuery);

$paidCount = 0;
$unpaidCount = 0;

if ($row = $paymentResult->fetch_assoc()) {
    $paidCount = (int) $row['paid_count'];
    $unpaidCount = (int) $row['unpaid_count'];
}

$total = $paidCount + $unpaidCount;
$paidPercentage = $total > 0 ? round(($paidCount / $total) * 100, 2) : 0;
$unpaidPercentage = $total > 0 ? round(($unpaidCount / $total) * 100, 2) : 0;

// ✅ Fix database connection issue
$sql = "SELECT COURSE_NAME FROM course";
$result = $mydb->query($sql); // ✅ Changed from $conn->query to $mydb->query

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}

// ✅ Return a single JSON response for both datasets
echo json_encode([
    'courseNames' => $courseNames,
    'courseCounts' => $courseCounts,
    'week' => $weekLabel,
    'enrollCount' => $enrollCount, // ✅ Fixed: Weekly enrollments are not overwritten
    'accepted' => $acceptedPercentage,
    'paid' => $paidPercentage,
    'unpaid' => $unpaidPercentage,
    'day' => $dayLabel,
    'dailyCount' => $dailyCount, // ✅ Fixed: Using the correct variable
    'departments' => $departments 
]);
?>