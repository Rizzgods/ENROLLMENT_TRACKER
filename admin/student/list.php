<?php
	 if (!isset($_SESSION['ACCOUNT_ID'])){
      redirect(web_root."admin/index.php");
     }

?>

<div class="row">
      <div class="col-lg-12">
       	 <div class="col-lg-6">
            <h1 class="page-header">List of Students </h1>
       		</div>
       		<div class="col-lg-6" >
			   <img width = "15%" style="float:right;" src="<?php echo web_root; ?>img/bcp_logo.png" >
       		</div>
       		</div>
        	<!-- /.col-lg-12 -->
   		 </div>

<!-- Add this right after the page header and before the table -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter Students</h3>
            </div>
            <div class="panel-body">
                <form method="GET" action="" class="form-inline">
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="course_filter" style="margin-right: 5px;">Course:</label>
                        <select name="course_filter" id="course_filter" class="form-control">
                            <option value="">All Courses</option>
                            <?php
                            $courseQuery = "SELECT COURSE_ID, COURSE_NAME FROM course ORDER BY COURSE_NAME";
                            $courseResult = $mydb->setQuery($courseQuery);
                            $courses = $mydb->loadResultList();
                            
                            foreach ($courses as $course) {
                                $selected = (isset($_GET['course_filter']) && $_GET['course_filter'] == $course->COURSE_ID) ? 'selected' : '';
                                echo '<option value="'.$course->COURSE_ID.'" '.$selected.'>'.$course->COURSE_NAME.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="status_filter" style="margin-right: 5px;">Status:</label>
                        <select name="status_filter" id="status_filter" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="accepted" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'accepted') ? 'selected' : ''; ?>>Accepted</option>
                            <option value="pending" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="rejected" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="payment_filter" style="margin-right: 5px;">Payment:</label>
                        <select name="payment_filter" id="payment_filter" class="form-control">
                            <option value="">All Payments</option>
                            <option value="Paid" <?php echo (isset($_GET['payment_filter']) && $_GET['payment_filter'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                            <option value="Unpaid" <?php echo (isset($_GET['payment_filter']) && $_GET['payment_filter'] == 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="index.php?view=list" class="btn btn-default">Reset</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Now modify the SQL query to include the filters
$sql = "SELECT 
    s.IDNO, 
    s.LNAME, 
    s.FNAME, 
    s.MNAME, 
    s.SEX, 
    s.BDAY, 
    s.HOME_ADD, 
    s.CONTACT_NO, 
    c.COURSE_NAME, 
    c.COURSE_ID,
    sa.SCHEDULE, 
    sa.STATUS, 
    sa.PAYMENT
FROM tblstudent s
JOIN course c ON s.COURSE_ID = c.COURSE_ID
LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
WHERE 1=1";

// Add condition for course filter if selected
if (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
    $sql .= " AND s.COURSE_ID = '" . $_GET['course_filter'] . "'";
}

// Add condition for status filter if selected
if (isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
    $sql .= " AND sa.STATUS = '" . $_GET['status_filter'] . "'";
} else {
    // If no status filter, use the original condition to show only accepted students
    $sql .= " AND sa.STATUS = 'accepted'";
}

// Add condition for payment filter if selected
if (isset($_GET['payment_filter']) && !empty($_GET['payment_filter'])) {
    $sql .= " AND sa.PAYMENT = '" . $_GET['payment_filter'] . "'";
}

// Add the original condition to exclude records that exist in student table
$sql .= " AND NOT EXISTS (
    SELECT 1 FROM student st WHERE st.id = s.IDNO
)";

$mydb->setQuery($sql);
?>

<!-- Add this just above the table to show active filters -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <strong>Filters:</strong> 
            <?php
            echo "Course: " . (isset($_GET['course_filter']) && !empty($_GET['course_filter']) ? 
                getCourseName($_GET['course_filter'], $mydb) : "All");
                
            echo " | Status: " . (isset($_GET['status_filter']) && !empty($_GET['status_filter']) ? 
                $_GET['status_filter'] : "Accepted");
                
            echo " | Payment: " . (isset($_GET['payment_filter']) && !empty($_GET['payment_filter']) ? 
                $_GET['payment_filter'] : "All");
            ?>
        </div>
    </div>
</div>

<?php
// Helper function to get course name
function getCourseName($courseId, $db) {
    $db->setQuery("SELECT COURSE_NAME FROM course WHERE COURSE_ID = '{$courseId}'");
    $result = $db->loadSingleResult();
    return $result->COURSE_NAME;
}
?>

	 		    <form action="controller.php?action=delete" Method="POST">  
			      <div class="table-responsive">			
				<table id="dash-table" class="table table-striped table-bordered table-hover table-responsive" style="font-size:12px" cellspacing="0">
				
				  <thead>
				  	<tr>
				  		<th>ID</th>
				  		<th>
				  		 <!-- <input type="checkbox" name="chkall" id="chkall" onclick="return checkall('selector[]');">  -->
				  		 Name</th>
				  		<th>Sex</th> 
				  		<th>Age</th>
				  		<th>Address</th>
				  		<th>Contact No.</th>
				  		<th>Course</th>
						  <th>Status</th>
						<th>Schedule</th>
						<th>Payment</th>
				  		<!-- <th>Status</th> -->
				  		<th width="14%" >Action</th>
				 
				  	</tr>	
				  </thead> 
				  <tbody>
				  	<?php  //`IDNO`, `FNAME`, `LNAME`, `MNAME`, `SEX`, `BDAY`, `BPLACE`,
				  	// `STATUS`, `AGE`, `NATIONALITY`, `RELIGION`, `CONTACT_NO`, `HOME_ADD`, `EMAIL`, `student_status`
					  $cur = $mydb->loadResultList();
				  
				  foreach ($cur as $result) {
					  if ($result->BDAY != '0000-00-00') {
						  $age = date_diff(date_create($result->BDAY), date_create('today'))->y;
					  } else {
						  $age = 'None';
					  }
					  
					  echo '<tr>';
					  echo '<td>' . $result->IDNO . '</td>';
					  echo '<td>' . $result->LNAME . ', ' . $result->FNAME . ' ' . $result->MNAME . '</td>';
					  echo '<td>' . $result->SEX . '</td>';
					  echo '<td>' . $age . '</td>';
					  echo '<td>' . $result->HOME_ADD . '</td>';
					  echo '<td>' . $result->CONTACT_NO . '</td>';
					  echo '<td>' . $result->COURSE_NAME . '</td>';
					  echo '<td>' . $result->STATUS . '</td>';
					  echo '<td>' . $result->SCHEDULE . '</td>';  // Displaying Schedule from studentaccount
					  echo '<td>' . $result->PAYMENT . '</td>';   // Displaying Payment Status from studentaccount
				  		 
					  echo '<td align="center" > 
					   <a title="Confirm" href="controller.php?action=confirm&IDNO=' . $result->IDNO . '" class="btn btn-success btn-xs">Confirm <span class="fa fa-info-circle fw-fa"></span></a>
                                <a title="Reject" href="controller.php?action=reject&IDNO=' . $result->IDNO . '" class="btn btn-danger btn-xs">Reject <span class="fa fa-info-circle fw-fa"></span></a>
                                <a title="View Information" href="index.php?view=view&id='.$result->IDNO.'"  class="btn btn-info btn-xs  ">View <span class="fa fa-info-circle fw-fa"></span></a>
				 </td>';
				  		// echo '<td align="center" > <a title="View Grades" href="index.php?view=grades&id='.$result->IDNO.'" class="btn btn-primary btn-xs" >Grades <span class="fa fa-info-circle fw-fa"></span> </a>
				  		// 			 </td>';
				  		echo '</tr>';
				  	} 
				  	?>
				  </tbody>
					
				</table>
 
				<!-- <div class="btn-group">
				  <a href="index.php?view=add" class="btn btn-default">New</a>
				  <button type="submit" class="btn btn-default" name="delete"><span class="glyphicon glyphicon-trash"></span> Delete Selected</button>
				</div>
 -->
			</div>
				</form>
	

</div> <!---End of container-->