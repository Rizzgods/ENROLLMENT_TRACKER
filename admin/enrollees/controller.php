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
	
	case 'confirm':
		if (isset($_GET['IDNO'])) {
			doConfirm($_GET['IDNO'], $mydb);  // Pass IDNO from the URL and the $mydb connection
		}
		break;
	
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
		"08:00 - 09:00", "09:00 - 10:00", "10:00 - 11:00", "11:00 - 12:00",
		"01:00 - 02:00", "02:00 - 03:00", "03:00 - 04:00", "04:00 - 05:00"
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


		$scheduleQuery = "SELECT SCHEDULE FROM studentaccount WHERE user_id = ?";
		$stmt = $db->conn->prepare($scheduleQuery);
		$stmt->bind_param("i", $IDNO);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$schedule = $row['SCHEDULE'];
		$stmt->close();

		if ($status == "approved") {
			$mail->Subject = "Enrollment Confirmation";
			$mail->Body = "<p>Hello $FNAME,</p>
				<p>Your enrollment has been successfully processed.</p>
				<p><b>Student ID:</b> $IDNO</p>
				<p><b>Schedule:</b> $schedule</p>
				<p>Welcome aboard!</p>";

		} else {
			$mail->Subject = "Enrollment Rejection";
			$mail->Body = "<p>Hello $FNAME,</p>
				<p>Unfortunately, your enrollment application has been rejected.</p>
				<p>Please contact the administration for further details.</p>";
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

	$updateaccount = "UPDATE studentaccount SET STATUS = 'accepted', WHERE IDNO = ?";
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

				$updateaccount = "UPDATE studentaccount SET STATUS = 'rejected', WHERE IDNO = ?";
				$stmt = $db->conn->prepare($updateaccount);
				$stmt->bind_param("i", $IDNO);
				$stmt->execute();
				$stmt->close();
			

				sendEmail($student['EMAIL'], $student['FNAME'], $student['LNAME'], "rejected", $IDNO,$db);

				message("Student Rejected", "success");
				redirect("index.php?view=success&IDNO=".$_GET['IDNO']);
				exit();
			}



			
			
		 
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

	 function doSubmitSubject(){
		global $mydb;

	 	 if (isset($_SESSION['admingvCart'])) {
				 	# code...
	 	 	$sql = "SELECT * FROM tblstudent WHERE IDNO=" .$_POST['IDNO'];
	 	 	$strRes = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
	 	 	$cid = mysqli_fetch_assoc($strRes);


	 	 	$sql = "SELECT * FROM course WHERE COURSE_ID=" . $cid['COURSE_ID'];
	 	 	$strRes = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
	 	 	$courseLevel = mysqli_fetch_assoc($strRes);

	 	 	$sem = new Semester();
			$resSem = $sem->single_semester();
			$_SESSION['SEMESTER'] = $resSem->SEMESTER; 


			$currentyear = date('Y');
			$nextyear =  date('Y') + 1;
			$sy = $currentyear .'-'.$nextyear;
			$_SESSION['SY'] = $sy;


		  
				
					$count_cart = count($_SESSION['admingvCart']);

			                for ($i=0; $i < $count_cart  ; $i++) {  

			                    $query = "SELECT * FROM `subject` s, `course` c WHERE s.COURSE_ID=c.COURSE_ID AND SUBJ_ID=" . $_SESSION['admingvCart'][$i]['subjectid'];
			                   	$resQuery = mysqli_query($mydb->conn,$query) or die(mysqli_error($mydb->conn));

			                   	while ($row = mysqli_fetch_array($resQuery)) {
			                   		# code...
			                   

			                     // $mydb->setQuery($query);
			                     // $cur = $mydb->loadResultList(); 
			                     //  foreach ($cur as $result) { 

			                      	$sql = "SELECT * FROM `studentsubjects` WHERE  `IDNO`=". $_POST['IDNO']." AND `SUBJ_ID`=".$row['SUBJ_ID'];
			                     	$resQuery = mysqli_query($mydb->conn,$query) or die(mysqli_error($mydb->conn));

			                   	while ($rows = mysqli_fetch_array($resQuery)) {

			                      // 	 $mydb->setQuery($query);
				                     // $cur = $mydb->loadResultList(); 
				                     //  foreach ($cur as $result) { 
				                      	
				                      	if (file_exists($rows['SUBJ_ID'])) {
				                      		# code...
				                      		$studentsubject = New StudentSubjects();
											$studentsubject->ATTEMP 	= $studentsubject->ATTEMP + 1; 
											$studentsubject->LEVEL 		= $courseLevel['COURSE_LEVEL'];
											$studentsubject->SEMESTER 	= $_SESSION['SEMESTER'];
											$studentsubject->SY 		= $_SESSION['SY'];
											$studentsubject->updateSubject($result->SUBJ_ID,$_POST['IDNO']);
				                      	}else{

				                      		$studentsubject = New StudentSubjects();
											$studentsubject->IDNO 		= $_POST['IDNO'];
											$studentsubject->SUBJ_ID	= $rows['SUBJ_ID'];
											$studentsubject->LEVEL 		= $courseLevel['COURSE_LEVEL'];
											$studentsubject->SEMESTER 	= $_SESSION['SEMESTER'];
											$studentsubject->SY 		= $_SESSION['SY'];
											$studentsubject->create();

											$grade = New Grade();
											$grade->IDNO     = $_POST['IDNO'];
											$grade->SUBJ_ID	 = $row['SUBJ_ID'];
											$grade->SEMS     = $_SESSION['SEMESTER'];
											$grade->create();

				                      	}
				                      }
 
									

									$sql = "INSERT INTO `schoolyr`
									(`AY`, `SEMESTER`, `COURSE_ID`, `IDNO`, `CATEGORY`, `DATE_RESERVED`, `DATE_ENROLLED`, `STATUS`)
									VALUES ('".$_SESSION['SY']."','".$_SESSION['SEMESTER']."','".$row['COURSE_ID']."','".$_POST['IDNO']."','ENROLLED','".date('Y-m-d')."','".date('Y-m-d')."','New');";
									$res = mysqli_query($mydb->conn,$sql) or die(mysqli_error($mydb->conn));
			                      }      
			                } 


							$query = "SELECT * FROM `tblstudent` WHERE `COURSE_ID`=". $cid['COURSE_ID'];
							$result = mysqli_query($mydb->conn,$query) or die(mysqli_error($mydb->conn));
							$numrow = mysqli_num_rows($result);
							// $maxrows = count($numrow);

			                if ($numrow > 40) {
								# code...
								$student = New Student();  
								$student->NewEnrollees =0;  
								$student->YEARLEVEL =  $courseLevel['COURSE_LEVEL'];
								$student->STUDSECTION = 2;
								$student->update($_POST['IDNO']);
							}else{
								$student = New Student();  
								$student->NewEnrollees =0;  
								$student->YEARLEVEL =  $courseLevel['COURSE_LEVEL'];
								$student->STUDSECTION = 1;
								$student->update($_POST['IDNO']);
							}

				  	// 		$student = New Student();  
							// $student->NewEnrollees =0;  
							// $student->YEARLEVEL = $courseLevel['COURSE_LEVEL'];
							// $student->update($_POST['IDNO']);
			              

			              

							message("Load has been added to the transferee enrollees!", "success");
							redirect("index.php?view=success&IDNO=".$_POST['IDNO']);
			
			              }
	 }
?>