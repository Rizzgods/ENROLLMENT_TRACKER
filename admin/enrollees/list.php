<?php

if (!isset($_SESSION['ACCOUNT_ID'])) {
    header("Location: ../index.php");
    exit();
}

require_once("../../include/initialize.php");

?>

<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-6">
            <h1 class="page-header">New Enrollees</h1>
        </div>
        <div class="col-lg-6">
            <img width="15%" style="float:right;" src="<?php echo web_root; ?>img/bcp_logo.png">
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>

<!-- Add Filter Panel -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter Enrollees</h3>
            </div>
            <div class="panel-body">
                <form method="GET" action="" class="form-inline">
                    <input type="hidden" name="view" value="list">
                    
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
                    
                    <!-- Status filter has been removed -->
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="gender_filter" style="margin-right: 5px;">Gender:</label>
                        <select name="gender_filter" id="gender_filter" class="form-control">
                            <option value="">All</option>
                            <option value="Male" <?php echo (isset($_GET['gender_filter']) && $_GET['gender_filter'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($_GET['gender_filter']) && $_GET['gender_filter'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="index.php?view=list" class="btn btn-default">Reset</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Active Filters Display -->
<?php if (isset($_GET['course_filter']) || isset($_GET['gender_filter'])): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <strong>Active Filters:</strong>
            <?php
            // Display course filter
            if (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
                $courseName = '';
                foreach ($courses as $course) {
                    if ($course->COURSE_ID == $_GET['course_filter']) {
                        $courseName = $course->COURSE_NAME;
                        break;
                    }
                }
                echo " Course: " . $courseName;
            }
            
            // Status filter display removed
            
            // Display gender filter
            if (isset($_GET['gender_filter']) && !empty($_GET['gender_filter'])) {
                echo (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) ? " | " : "";
                echo " Gender: " . $_GET['gender_filter'];
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<form action="controller.php?action=delete" method="POST">
    <div class="table-responsive">
        <table id="dash-table" class="table table-striped table-bordered table-hover table-responsive" style="font-size:12px" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Status</th>
                    <th>Course</th>
                    <th width="14%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch latest data from the database with filters
                $query = "SELECT * FROM `tblstudent` s, `course` c WHERE s.COURSE_ID = c.COURSE_ID AND NewEnrollees = 1";

                // Apply course filter
                if (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
                    $query .= " AND s.COURSE_ID = '".$_GET['course_filter']."'";
                }
                
                // Status filter removed - but keeping the default filter for 'New' students
                $query .= " AND s.student_status = 'New'"; // Default filter
                
                // Apply gender filter
                if (isset($_GET['gender_filter']) && !empty($_GET['gender_filter'])) {
                    $query .= " AND s.SEX = '".$_GET['gender_filter']."'";
                }

                $mydb->setQuery($query);
                $cur = $mydb->loadResultList();

                foreach ($cur as $result) {
                    $age = ($result->BDAY != '0000-00-00') ? date_diff(date_create($result->BDAY), date_create('today'))->y : 'None';
                    echo '<tr>';
                    echo '<td>' . $result->IDNO . '</td>';
                    echo '<td>' . $result->LNAME . ', ' . $result->FNAME . ' ' . $result->MNAME . '</td>';
                    echo '<td>' . $result->SEX . '</td>';
                    echo '<td>' . $age . '</td>';
                    echo '<td>' . $result->HOME_ADD . '</td>';
                    echo '<td>' . $result->CONTACT_NO . '</td>';
                    echo '<td>' . $result->student_status . '</td>';
                    echo '<td>' . $result->COURSE_NAME . '</td>';
                    echo '<td align="center">
                            <a title="Confirm" href="controller.php?action=confirm&IDNO=' . $result->IDNO . '" class="btn btn-success btn-xs">Confirm <span class="fa fa-info-circle fw-fa"></span></a>
                            <a title="Reject" href="controller.php?action=reject&IDNO=' . $result->IDNO . '" class="btn btn-danger btn-xs">Reject <span class="fa fa-info-circle fw-fa"></span></a>
                            <a title="View Information" href="index.php?view=view&id='.$result->IDNO.'"  class="btn btn-info btn-xs  ">View <span class="fa fa-info-circle fw-fa"></span></a>
                            </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</form>

<!-- Display count of filtered results -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-success">
            <strong>Total Records Found:</strong> <?php echo count($cur); ?>
        </div>
    </div>
</div>
</div> <!---End of container-->