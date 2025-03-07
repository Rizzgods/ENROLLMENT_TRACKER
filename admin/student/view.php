<?php  
    $student = New Student();
    $res = $student->single_student($_GET['id']);

    $studentdetails = New StudentDetails();
    $resguardian = $studentdetails->single_StudentDetails($_GET['id']);

    $course = New Course();
    $resCourse = $course->single_course($res->COURSE_ID);
    
    // Updated database connection with correct credentials for production server
    $servername = "localhost";
    $username = "admi_greenvalley";
    $password = "xr9%kxu%*my^+kf2";
    $dbname = "admi_dbgreenvalley";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        exit("Connection failed: " . $conn->connect_error);
    }

    // Fix: Use $_GET['id'] instead of $_SESSION['user_id'] since this is an admin view of student details
    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE IDNO = ?");
    $stmt->bind_param("s", $_GET['id']);  // Use the ID from the URL
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!-- Course Filter Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filter Students</h4>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Course:</label>
                                <select name="course_filter" class="form-control">
                                    <option value="">All Courses</option>
                                    <?php
                                    // Get all courses from the database
                                    $course_query = "SELECT DISTINCT COURSE_NAME, COURSE_ID FROM course ORDER BY COURSE_NAME";
                                    $course_result = $conn->query($course_query);
                                    
                                    while ($course_row = $course_result->fetch_assoc()) {
                                        $selected = (isset($_GET['course_filter']) && $_GET['course_filter'] == $course_row['COURSE_ID']) ? 'selected' : '';
                                        echo "<option value='{$course_row['COURSE_ID']}' {$selected}>{$course_row['COURSE_NAME']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status_filter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="New" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'New') ? 'selected' : ''; ?>>New</option>
                                    <option value="Continuing" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'Continuing') ? 'selected' : ''; ?>>Continuing</option>
                                    <option value="Transferee" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'Transferee') ? 'selected' : ''; ?>>Transferee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Apply Filter</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="view.php?id=<?php echo $_GET['id']; ?>" class="btn btn-secondary btn-block">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Then modify the student query to apply the filters -->
<?php
// If course filter is set, add it to query
$filter_condition = "";
if (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
    $course_filter = $_GET['course_filter'];
    $filter_condition .= " AND s.COURSE_ID = '$course_filter'";
}

// If status filter is set, add it to query
if (isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
    $status_filter = $_GET['status_filter'];
    $filter_condition .= " AND s.student_status = '$status_filter'";
}

// Modify your existing query to include the filter
$sql = "SELECT s.*, c.COURSE_NAME 
        FROM tblstudent s 
        JOIN course c ON s.COURSE_ID = c.COURSE_ID 
        WHERE s.IDNO = ? $filter_condition";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
?>



<div class="row">
    <!-- Left Panel: Student Info -->
    <div class="col-sm-3">
        <div class="panel">
            <a href="" data-target="#myModal" data-toggle="modal"></a>
        </div>
        <ul class="list-group">
            <li class="list-group-item text-right">
                <span class="pull-left"><strong>Real Name</strong></span>
                <?php echo $res->FNAME . ' ' . $res->LNAME; ?>
            </li>
            <li class="list-group-item text-right">
                <span class="pull-left"><strong>Course</strong></span>
                <?php echo $resCourse->COURSE_NAME . '-' . $res->YEARLEVEL; ?>
            </li>
            <li class="list-group-item text-right">
                <span class="pull-left"><strong>Status</strong></span>
                <?php echo $res->student_status; ?>
            </li>
        </ul>
    </div>


    
    <!-- Right Panel: Documents -->
    <div class="col-sm-8">
        <div class="panel">
            <ul class="list-group">
                <?php 
                $documents = [
                    'form_138' => 'Form 138',
                    'form_137' => 'Form 137',
                    'good_moral' => 'Good Moral',
                    'psa_birthCert' => 'PSA Birth Certificate',
                    'Brgy_clearance' => 'Barangay Clearance',
                    'tor' => 'Transcript of Records',
                    'honor_dismissal' => 'Honorable Dismissal'
                ];

                foreach ($documents as $field => $label):
                  
                    $hasDocument = !empty($res->$field);
                    $documentSrc = !empty($res->$field) ? 'data:image/jpeg;base64,' . base64_encode($res->$field) : '#';
                ?>
                    <li class="list-group-item text-right">
    <span class="pull-left"><strong><?php echo $label; ?></strong></span>
    <?php if ($hasDocument): ?>
      <a href="javascript:void(0);" class="open-modal" 
           data-img="<?php echo htmlspecialchars($documentSrc, ENT_QUOTES, 'UTF-8'); ?>" 
           data-target="#imageModal">View</a>
    <?php else: ?>
        <span class="text-muted">No document uploaded</span>
    <?php endif; ?>
</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Modal for Viewing Images -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" style="font-size: 3rem;">&times;</span>
</button>

            </div>
            <div class="modal-body text-center">
                <img style="width: 50vh;" id="modalImage" src="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
</div>



                

         
         
        <!--/col-3-->
<div class="col-sm-9"> 
   <!-- `IDNO`, `FNAME`, `LNAME`, `MNAME`, `SEX`, `BDAY`, `BPLACE`, `STATUS`, `AGE`, `NATIONALITY`,
 `RELIGION`, `CONTACT_NO`, `HOME_ADD`, `EMAIL`, `ACC_PASSWORD`, `student_status`, `schedID`, `course_year` -->
<?php
  $currentyear = date('Y');
  $nextyear =  date('Y') + 1;
  $sy = $currentyear .'-'.$nextyear;
  $_SESSION['SY'] = $sy;
  // $newDate    = Carbon::createFromFormat('Y-m-d',$_SESSION['SY'] )->addYear(1);

?>

<form action="controller.php?action=edit" class="form-horizontal" method="post" >
  <div class="table-responsive">
  <div class="col-md-8"><h2>Student Information</h2></div>
    <table class="table"> 
    <tr>
        <td><label>Id</label></td>
        <td >
          <input class="form-control input-md" readonly id="IDNO" name="IDNO" placeholder="Student Id" type="text" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
        </td>
        <td colspan="4"></td>

      </tr>
      <tr>
        <td><label>Firstname</label></td>
        <td>
          <input required="true"   class="form-control input-md" id="FNAME" name="FNAME" placeholder="First Name" type="text" readonly value="<?php echo  $res->FNAME; ?>">
        </td>
        <td><label>Lastname</label></td>
        <td colspan="2">
          <input required="true"  class="form-control input-md" id="LNAME" name="LNAME" placeholder="Last Name" type="text" readonly value="<?php echo $res->LNAME; ?>">
        </td> 
        <td>
          <input class="form-control input-md" id="MI" name="MI" placeholder="MI" type="text" readonly value="<?php echo $res->MNAME; ?>">
        </td>
      </tr>
      <tr>
        <td><label>Address</label></td>
        <td colspan="5"  >
        <input required="true"  class="form-control input-md" id="PADDRESS" name="PADDRESS" placeholder="Permanent Address" type="text" readonly value="<?php echo $res->HOME_ADD; ?>">
        </td> 
      </tr>
      <tr>
      <td><label>Sex </label></td> 
<td colspan="2">
    <label>
        <?php
        if ($res->SEX == 'Male') {
            echo '<input id="optionsRadios1" name="optionsRadios" type="radio" value="Female" disabled> Female 
                  <input id="optionsRadios2" name="optionsRadios" type="radio" value="Male" checked disabled> Male';
        } else {
            echo '<input id="optionsRadios1" name="optionsRadios" type="radio" value="Female" checked disabled> Female 
                  <input id="optionsRadios2" name="optionsRadios" type="radio" value="Male" disabled> Male';
        }
        ?>
    </label>
</td>
        <td><label>Date of birth</label></td>
        <td colspan="2"> 
        <div class="input-group " >
                  <div class="input-group-addon"> 
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input  required="true" name="BIRTHDATE"  id="BIRTHDATE"  type="text" class="form-control input-md" readonly  data-inputmask="'alias': 'mm/dd/yyyy'" data-mask value="<?php echo date_format(date_create($res->BDAY),'m/d/Y'); ?>">
           </div>             
        </td>
         
      </tr>
      <tr><td><label>Place of Birth</label></td>
        <td colspan="5">
        <input required="true"  class="form-control input-md" id="BIRTHPLACE" name="BIRTHPLACE" readonly placeholder="Place of Birth" type="text" value="<?php echo $res->BPLACE; ?>">
         </td>
      </tr>
      <tr>
        <td><label>Nationality</label></td>
        <td colspan="2"><input required="true"  class="form-control input-md" id="NATIONALITY" readonly name="NATIONALITY" placeholder="Nationality" type="text" value="<?php echo $res->NATIONALITY; ?>">
              </td>
        <td><label>Religion</label></td>
        <td colspan="2"><input  required="true" class="form-control input-md" id="RELIGION" readonly name="RELIGION" placeholder="Religion" type="text" value="<?php echo $res->RELIGION; ?>">
        </td>
        
      </tr>
      <tr>
      <td><label>Contact No.</label></td>
        <td colspan="3"><input required="true"  class="form-control input-md" id="CONTACT" readonly name="CONTACT" placeholder="Contact Number" type="text" value="<?php echo $res->CONTACT_NO; ?>">
              </td>
              <td><label>Civil Status</label></td>
<td colspan="2">
    <select class="form-control input-sm" name="CIVILSTATUS" disabled>
        <option value="<?php echo $res->STATUS; ?>"><?php echo $res->STATUS; ?></option>
        <option value="Single">Single</option>
        <option value="Married">Married</option> 
        <option value="Widow">Widow</option>
    </select>
</td>
      </tr> 
     
      <tr>
        <td><label>Guardian</label></td>
        <td colspan="2">
          <input required="true"  class="form-control input-md" id="GUARDIAN" readonly name="GUARDIAN" placeholder="Parents/Guardian Name" type="text"value="<?php echo isset($resguardian->GUARDIAN) ? $resguardian->GUARDIAN : ''; ?>">
        </td>
        <td><label>Contact No.</label></td>
        <td colspan="2"><input  required="true" class="form-control input-md" readonly id="GCONTACT" name="GCONTACT" placeholder="Contact Number" type="text"value="<?php echo isset($resguardian->GCONTACT) ? $resguardian->GCONTACT : ''; ?>"></td>
      </tr>
      <tr>
      <td></td>
        <td colspan="5">  
          <a class="btn btn-success btn-lg" href="index.php" type="submit">Back</a>
        </td>
      </tr>
    </table>
  </div>
</form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/modal.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Add this after the student info form section -->

<!-- Student List Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Student List 
                    <?php 
                    if(isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
                        $course_name_query = "SELECT COURSE_NAME FROM course WHERE COURSE_ID = ?";
                        $stmt = $conn->prepare($course_name_query);
                        $stmt->bind_param("s", $_GET['course_filter']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($row = $result->fetch_assoc()) {
                            echo "- " . $row['COURSE_NAME'];
                        }
                    }
                    
                    if(isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
                        echo " (" . $_GET['status_filter'] . ")";
                    }
                    ?>
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query to get filtered students
                            $list_sql = "SELECT s.*, c.COURSE_NAME 
                                        FROM tblstudent s 
                                        JOIN course c ON s.COURSE_ID = c.COURSE_ID 
                                        WHERE 1=1 $filter_condition 
                                        ORDER BY s.LNAME";
                            
                            $list_result = $conn->query($list_sql);
                            
                            if ($list_result->num_rows > 0) {
                                while ($row = $list_result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['IDNO']}</td>";
                                    echo "<td>{$row['LNAME']}, {$row['FNAME']} {$row['MNAME']}</td>";
                                    echo "<td>{$row['COURSE_NAME']}</td>";
                                    echo "<td>{$row['student_status']}</td>";
                                    echo "<td>
                                          <a title='View Information' href='index.php?view=view&id={$row['IDNO']}' class='btn btn-info btn-xs'>View <span class='fa fa-info-circle fw-fa'></span></a>
                                          <a title='Confirm' href='controller.php?action=confirm&IDNO={$row['IDNO']}' class='btn btn-success btn-xs'>Confirm <span class='fa fa-check-circle fw-fa'></span></a>
                                          <a title='Reject' href='controller.php?action=reject&IDNO={$row['IDNO']}' class='btn btn-danger btn-xs'>Reject <span class='fa fa-times-circle fw-fa'></span></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No students found matching the criteria</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <small class="text-muted">Total students: <?php echo $list_result->num_rows; ?></small>
            </div>
        </div>
    </div>
</div>

<a title="Confirm" href="controller.php?action=confirm&IDNO=' . $result->IDNO . '" class="btn btn-success btn-xs">Confirm <span class="fa fa-info-circle fw-fa"></span></a>
                                <a title="Reject" href="controller.php?action=reject&IDNO=' . $result->IDNO . '" class="btn btn-danger btn-xs">Reject <span class="fa fa-info-circle fw-fa"></span></a>
                              <a title="View Information" href="index.php?view=view&id='.$result->IDNO.'"  class="btn btn-info btn-xs  ">View <span class="fa fa-info-circle fw-fa"></span></a>

