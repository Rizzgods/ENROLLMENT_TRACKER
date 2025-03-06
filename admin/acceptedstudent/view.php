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

    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE IDNO = ?");
    $stmt->bind_param("s", $_GET['id']); // Using the ID from the URL rather than session
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







 