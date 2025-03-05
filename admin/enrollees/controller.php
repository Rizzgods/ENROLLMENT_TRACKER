<?php
require_once ("../../include/initialize.php");
	 if (!isset($_SESSION['ACCOUNT_ID'])){
      redirect(web_root."admin/index.php");

	  
     }
	 use PHPMailer\PHPMailer\PHPMailer;
	 use PHPMailer\PHPMailer\Exception;

	 require_once __DIR__ . '/../../vendor/autoload.php';
$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	

	
case 'doadd' :
	doAddsubject();
	break;
case 'removed' :
	doRemoveCart();
	break;
case 'addsubjecttrans' :
	doSubmitSubject();
	break;

case 'addcreditsubject' :
	doAddCreditSubject();
	break;
}


if (isset($_GET['action']) && isset($_GET['IDNO'])) {
    $action = $_GET['action'];
    $IDNO = $_GET['IDNO'];

    if ($action == "confirm") {
        doConfirm($IDNO, $mydb);
    } elseif ($action == "reject") {
        rejectStudent($IDNO, $mydb);
    }
}


function assignSchedule($db) {
	// Define available schedule slots (1-hour range)
	$availableSlots = [
		"08:00am - 09:00am", "09:00am - 10:00am", "10:00am - 11:00am", "11:00am - 12:00pm",
		"01:00pm - 02:00pm", "02:00pm - 03:00pm", "03:00pm - 04:00pm", "04:00pm - 05:00pm"
	];

	// Loop through each time slot and check if there is space
	foreach ($availableSlots as $slot) {
		$countQuery = "SELECT COUNT(*) AS student_count FROM studentaccount WHERE SCHEDULE = ?";
		$stmt = $db->conn->prepare($countQuery);
		$stmt->bind_param("s", $slot);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();

		// If there are less than 200 students in this slot, assign it
		if ($row['student_count'] < 200) {
			return $slot;
		}
	}

	// If all slots are full, return 'TBA'
	return "TBA";


	
}

function sendEmail($EMAIL, $FNAME, $LNAME, $status, $IDNO, $db) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'taranavalvista@gmail.com'; // Your email
        $mail->Password   = 'kdiq oeqm cuyr yhuz'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->isHTML(true);

        // Recipients
        $mail->setFrom('taranavalvista@gmail.com', 'Enrollment Team');
        $mail->addAddress($EMAIL, $FNAME . ' ' . $LNAME);

        // Get the absolute URL for the logo
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $logo_url = $protocol . $host . '/onlineenrolmentsystem/assets/logo.png';

        // Get schedule information
        $scheduleQuery = "SELECT SCHEDULE FROM studentaccount WHERE user_id = ?";
        $stmt = $db->conn->prepare($scheduleQuery);
        $stmt->bind_param("i", $IDNO);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $schedule = $row['SCHEDULE'] ?? 'Not Assigned';
        $stmt->close();

        if ($status == "approved") {
            $mail->Subject = "Enrollment Confirmation";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
                    <!-- Header with Logo -->
                    <div style='background-color: #1a56db; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
                        <img src='{$logo_url}' alt='Bestlink Logo' style='max-height: 80px; margin-bottom: 10px;'>
                        <h1 style='color: white; margin: 0; font-size: 24px;'>BESTLINK ENROLLMENT SYSTEM</h1>
                    </div>
                    
                    <!-- Email Content -->
                    <div style='background-color: #f9fafb; border-radius: 0 0 8px 8px; padding: 20px; border: 1px solid #e5e7eb; border-top: none;'>
                        <h3 style='color: #4A5568; margin-top: 0;'>Hello $FNAME,</h3>
                        <p>Your enrollment has been <strong style='color: #10B981;'>APPROVED</strong>!</p>
                        
                        <div style='background-color: white; border-radius: 8px; padding: 15px; margin: 20px 0; border: 1px solid #e5e7eb;'>
                            <p style='font-weight: bold; color: #4A5568; margin-top: 0;'>Student Information:</p>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb; width: 40%;'><b>Student ID:</b></td>
                                    <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$IDNO</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'><b>Full Name:</b></td>
                                    <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$FNAME $LNAME</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'><b>Schedule:</b></td>
                                    <td style='padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>$schedule</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div style='background-color: #d1fae5; border-left: 4px solid #10B981; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                            <p style='margin: 0;'><strong>Welcome to Bestlink College of the Philippines!</strong> Your enrollment has been successfully processed. Please log in to your student account to view your complete schedule and course details.</p>
                        </div>
                        
                        <p>If you have any questions, please contact us at <a href='mailto:support@bestlink.edu.ph' style='color: #3182CE;'>support@bestlink.edu.ph</a>.</p>
                        
                        <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px;'>
                            <p style='margin-bottom: 5px;'><b>Thank you for enrolling!</b></p>
                            <p style='margin-top: 0; color: #6B7280;'>Bestlink Enrollment Team</p>
                        </div>
                    </div>
                </div>
            ";
        } else {
            $mail->Subject = "Enrollment Application Status";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
                    <!-- Header with Logo -->
                    <div style='background-color: #1a56db; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
                        <img src='{$logo_url}' alt='Bestlink Logo' style='max-height: 80px; margin-bottom: 10px;'>
                        <h1 style='color: white; margin: 0; font-size: 24px;'>BESTLINK ENROLLMENT SYSTEM</h1>
                    </div>
                    
                    <!-- Email Content -->
                    <div style='background-color: #f9fafb; border-radius: 0 0 8px 8px; padding: 20px; border: 1px solid #e5e7eb; border-top: none;'>
                        <h3 style='color: #4A5568; margin-top: 0;'>Hello $FNAME,</h3>
                        <p>We have reviewed your enrollment application.</p>
                        
                        <div style='background-color: #fee2e2; border-left: 4px solid #EF4444; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                            <p style='margin: 0;'>Unfortunately, after careful consideration, your enrollment application has not been approved at this time.</p>
                        </div>
                        
                        <p>Please contact the administration office for more information about your application status. We're here to help you understand the reason and explore your options moving forward.</p>
                        
                        <div style='background-color: #eff6ff; border-radius: 8px; padding: 15px; margin: 20px 0; border: 1px solid #dbeafe;'>
                            <p style='font-weight: bold; color: #1E40AF; margin-top: 0;'>Contact Information:</p>
                            <p style='margin-bottom: 5px;'><b>Phone:</b> (123) 456-7890</p>
                            <p style='margin-bottom: 5px;'><b>Email:</b> <a href='mailto:admissions@bestlink.edu.ph' style='color: #3182CE;'>admissions@bestlink.edu.ph</a></p>
                            <p style='margin-bottom: 0;'><b>Office Hours:</b> Monday-Friday, 8:00am - 5:00pm</p>
                        </div>
                        
                        <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px;'>
                            <p style='margin-bottom: 5px;'>Thank you for your interest in Bestlink College of the Philippines.</p>
                            <p style='margin-top: 0; color: #6B7280;'>Bestlink Enrollment Team</p>
                        </div>
                    </div>
                </div>
            ";
        }

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
    }
}

function doConfirm($IDNO, $db){
global $mydb;
$sem = new Semester();
$resSem = $sem->single_semester();
$_SESSION['SEMESTER'] = $resSem->SEMESTER; 

$updateStatus = "UPDATE tblstudent SET student_status = 'approved' WHERE IDNO = ?";
    $stmt = $db->conn->prepare($updateStatus);
    $stmt->bind_param("i", $IDNO);
    $stmt->execute();
    $stmt->close();

	$schedule = assignSchedule($db);

	$updateSchedule = "UPDATE studentaccount SET SCHEDULE = ? WHERE user_id = ?";
    $stmt2 = $db->conn->prepare($updateSchedule);
    $stmt2->bind_param("si", $schedule, $IDNO);
    $stmt2->execute();
    $stmt2->close();

	$updateStatus = "UPDATE tblstudent SET student_status = 'approved', NewEnrollees = 0 WHERE IDNO = ?";
    $stmt = $db->conn->prepare($updateStatus);
    $stmt->bind_param("i", $IDNO);
    $stmt->execute();
    $stmt->close();

	$updateaccount = "UPDATE studentaccount SET STATUS = 'accepted' WHERE user_id = ?";
    $stmt = $db->conn->prepare($updateaccount);
    $stmt->bind_param("i", $IDNO);
    $stmt->execute();
    $stmt->close();



$currentyear = date('Y');
$nextyear =  date('Y') + 1;
$sy = $currentyear .'-'.$nextyear;
$_SESSION['SY'] = $sy;
 
		 
		 $sql = "SELECT * FROM tblstudent WHERE IDNO =" .$_GET['IDNO'];
		 $resQuery = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
		 $studcourse = mysqli_fetch_assoc($resQuery);

		// $student = New Student(); 
		
		// $studcourse = $student->single_student($_GET['IDNO']);
 

		 $sql = "SELECT * FROM `subject` s, `course` c 
					WHERE s.COURSE_ID=c.COURSE_ID AND s.COURSE_ID=".$studcourse['COURSE_ID']." AND SEMESTER='".$_SESSION['SEMESTER']."'";

				 $EMAIL = $studcourse['EMAIL'];
   				 $FNAME = $studcourse['FNAME'];
   				 $LNAME = $studcourse['LNAME'];
   				 $MI = $studcourse['MNAME'];
    			$COURSEID = $studcourse['COURSE_ID'];
 			

				


			$query = "SELECT * FROM `tblstudent` WHERE `COURSE_ID`=".$studcourse['COURSE_ID'];
			$result = mysqli_query($mydb->conn,$query) or die(mysqli_error($mydb->conn));
			$numrow = mysqli_num_rows($result);
			// $maxrows = count($numrow);

		

			unset($_SESSION['SEMESTER']);
			unset($_SESSION['SY']);

			sendEmail($EMAIL,$FNAME,$LNAME,"approved",$IDNO, $db);
			message("Regular loads has been added to the new enrollees!", "success");
			redirect("index.php?view=success&IDNO=".$_GET['IDNO']);

}
			function rejectStudent($IDNO, $db) {
				$updateStatus = "UPDATE tblstudent SET student_status = 'rejected' WHERE IDNO = ?";
				$stmt = $db->conn->prepare($updateStatus);
				$stmt->bind_param("i", $IDNO);
				$stmt->execute();
				$stmt->close();

				$sql = "SELECT EMAIL, FNAME, LNAME FROM tblstudent WHERE IDNO = ?";
				$stmt2 = $db->conn->prepare($sql);
				$stmt2->bind_param("i", $IDNO);
				$stmt2->execute();
				$result = $stmt2->get_result();
				$student = $result->fetch_assoc();
				$stmt2->close();
			

				sendEmail($student['EMAIL'], $student['FNAME'], $student['LNAME'], "rejected", $IDNO,$db);

				message("Student Rejected", "success");
				redirect("index.php?view=success&IDNO=".$_GET['IDNO']);
				exit();
			}


			
			
		 
	 function doAddCreditSubject(){
global $mydb;
 
	 		 
	 		$subjid  = $_POST['SUBJ_ID'];
	 		$idno    = $_POST['IDNO'];
	 		$first   = $_POST['FIRSTGRADING'];
	 		$second  = $_POST['SECONDGRADING'];
	 		$third   = $_POST['THIRDGRADING'];
	 		$fourth  = $_POST['FOURTHGRADING'];
	 		$ave 	 = $_POST['AVERAGE'];
	 		$SEMESTER = $_POST['SEMESTER'];


	 		$sql = "SELECT * FROM studentsubjects WHERE IDNO = ". $idno ." AND SUBJ_ID=".$subjid;
	 		$result = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
	 		$maxrows = mysqli_num_rows($result);

	 		if ($maxrows > 0) {
	 			# code...
	 			message("Subject has already credited.", "error");
				redirect("index.php?view=addCredit&IDNO=".$idno);
	 		}else{

	 			if ($ave > 74.4) {
	 			# code...
		 

				$currentyear = date('Y');
				$nextyear =  date('Y') + 1;
				$sy = $currentyear .'-'.$nextyear;
				$_SESSION['SY'] = $sy;
 
				$studentsubject = New StudentSubjects();
				$studentsubject->IDNO 		= $idno;
				$studentsubject->SUBJ_ID	= $subjid;
				$studentsubject->LEVEL 		= 1;
				$studentsubject->SEMESTER 	= $SEMESTER;
				$studentsubject->SY 		= $_SESSION['SY'];
				$studentsubject->ATTEMP 	= 1; 
				$studentsubject->create();


				$grade = New Grade();
				$grade->IDNO 	 = $idno;
				$grade->SUBJ_ID	 = $subjid;
				$grade->FIRST 	 = $first;
				$grade->SECOND 	 = $second;
				$grade->THIRD 	 = $third;
				$grade->FOURTH 	 = $fourth;
				$grade->AVE 	 = $ave;
				$grade->create();
 
				unset($_SESSION['SY']);

				message("Subject has been credited.", "success");
				redirect("index.php?view=addCredit&IDNO=".$idno);
			 	}else{

			 		$currentyear = date('Y');
					$nextyear =  date('Y') + 1;
					$sy = $currentyear .'-'.$nextyear;
					$_SESSION['SY'] = $sy;
	 
					$studentsubject = New StudentSubjects();
					$studentsubject->IDNO 		= $idno;
					$studentsubject->SUBJ_ID	= $subjid;
					$studentsubject->LEVEL 		= 1;
					$studentsubject->SEMESTER 	= $SEMESTER;
					$studentsubject->SY 		= $_SESSION['SY'];
					$studentsubject->ATTEMP 	= 1; 
					$studentsubject->create();


					$grade = New Grade();
					$grade->IDNO 	 = $idno;
					$grade->SUBJ_ID	 = $subjid;
					$grade->FIRST 	 = $first;
					$grade->SECOND 	 = $second;
					$grade->THIRD 	 = $third;
					$grade->FOURTH 	 = $fourth;
					$grade->AVE 	 = $ave;
					$grade->create();
	 
					unset($_SESSION['SY']);


			 		message("The subject does not credit.", "error");
					redirect("index.php?view=addCredit&IDNO=".$idno);
			 	}

	 		}
	 		
 
	 }



	function doAddsubject(){
global $mydb;
if (isset($_GET['id'])) { 




$subject = New Subject();
$subj = $subject->single_subject($_GET['id']); 

	$sql = "SELECT * FROM `grades` g, `subject` s WHERE g.`SUBJ_ID`=s.`SUBJ_ID` AND `SUBJ_CODE`='" .$subj->PRE_REQUISITE. "' AND AVE < 75 AND IDNO=". $_GET['IDNO'];
 	$result = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
 	$row = mysqli_fetch_assoc($result);

 	if (isset($row['SUBJ_CODE'])) {
 	?>
		<script type="text/javascript">
			alert('You must take the pre-requisite first before taking up this subject.')
			window.location = "index.php?view=addCredit&IDNO=<?php echo  $_GET['IDNO']; ?>";
		</script>
 	<?php
	 }else{


	$sql = "SELECT * FROM `grades`  WHERE REMARKS !='Drop' AND `SUBJ_ID`='" .$_GET['id']. "'   AND IDNO=". $_GET['IDNO'];
	$result = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
 	$row = mysqli_fetch_assoc($result);



 		if (isset($row['SUBJ_ID'])) {
			# code...
		if ($row['AVE'] > 0 && $row['AVE'] < 75 ) {
			# code...
			?>
			<script type="text/javascript">
				alert('This subject is under taken.')
				window.location = "index.php?view=addCredit&IDNO=<?php echo  $_GET['IDNO']; ?>";
			</script>
	 	<?php
		}elseif ($row['AVE']==0) {
			# code...
			?>
			<script type="text/javascript">
				alert('This subject is under taken.')
			window.location = "index.php?view=addCredit&IDNO=<?php echo  $_GET['IDNO']; ?>";
			</script>
	 	<?php
		}elseif ($row['AVE'] > 74) {
			# code...
		
		?>
			<script type="text/javascript">
				alert('You have already taken this subject.')
				window.location = "index.php?view=addCredit&IDNO=<?php echo  $_GET['IDNO']; ?>";
			</script>
	 	<?php
	 }
	}else{
		 
				adminaddtocart($_GET['id']);

			 	redirect(web_root."admin/enrollees/index.php?view=addCredit&IDNO=".$_GET['IDNO']);
		 
	} 
	}
 }
}

		
	 

	function doDelete(){
global $mydb;
		
		// if (isset($_POST['selector'])==''){
		// message("Select the records first before you delete!","info");
		// redirect('index.php');
		// }else{

		// $id = $_POST['selector'];
		// $key = count($id);

		// for($i=0;$i<$key;$i++){

		// 	$course = New User();
		// 	$course->delete($id[$i]);

		
			$id = 	$_GET['id'];

			$course = New Course();
 		 	$course->delete($id);
			 
			message("Course already Deleted!","info");
			redirect('index.php');
		// }
		// }

		
	}

	 function doRemoveCart(){
	 	adminremovetocart($_GET['id']);
		redirect(web_root."admin/enrollees/index.php?view=enrolledsubject&IDNO=".$_GET['IDNO']);
	 }

	 