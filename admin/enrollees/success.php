<?php

if (!isset($_GET['IDNO'])) {
   redirect("index.php");
}
$sem = new Semester();
$resSem = $sem->single_semester();
$_SESSION['SEMESTER'] = $resSem->SEMESTER; 

$currentyear = date('Y');
$nextyear =  date('Y') + 1;
$sy = $currentyear .'-'.$nextyear;
$_SESSION['SY'] = $sy;

     $student = New Student(); 
     $studres = $student->single_student($_GET['IDNO'])
     
?>
