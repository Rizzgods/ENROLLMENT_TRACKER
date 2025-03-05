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
                // Fetch latest data from the database
                $mydb->setQuery("SELECT * FROM `tblstudent` s, `course` c WHERE s.COURSE_ID = c.COURSE_ID AND NewEnrollees = 1 AND student_status = 'New'");
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
                    if ($result->student_status == 'New') {
                        echo '<td align="center">
                                <a title="Confirm" href="controller.php?action=confirm&IDNO=' . $result->IDNO . '" class="btn btn-success btn-xs">Confirm <span class="fa fa-info-circle fw-fa"></span></a>
                                <a title="Reject" href="controller.php?action=reject&IDNO=' . $result->IDNO . '" class="btn btn-danger btn-xs">Reject <span class="fa fa-info-circle fw-fa"></span></a>
                                <a title="View Information" href="index.php?view=view&id='.$result->IDNO.'"  class="btn btn-info btn-xs  ">View <span class="fa fa-info-circle fw-fa"></span></a>
                                </td>';
                    } else {
                        echo '<td align="center">
                                <a title="Add Subject" href="index.php?view=addCredit&IDNO=' . $result->IDNO . '" class="btn btn-info btn-xs">Add Subject <span class="fa fa-info-circle fw-fa"></span></a>
                              </td>';
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</form>
</div> <!---End of container-->