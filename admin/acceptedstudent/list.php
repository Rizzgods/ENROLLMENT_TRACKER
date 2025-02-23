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
						<th>Payment</th>
				  		<!-- <th>Status</th> -->
				  		<th width="14%" >Action</th>
				 
				  	</tr>	
				  </thead> 
				  <tbody>
				  	<?php  //`IDNO`, `FNAME`, `LNAME`, `MNAME`, `SEX`, `BDAY`, `BPLACE`,
				  	// `STATUS`, `AGE`, `NATIONALITY`, `RELIGION`, `CONTACT_NO`, `HOME_ADD`, `EMAIL`, `student_status`
					  $mydb->setQuery("
					  SELECT 
						  s.IDNO, 
						  s.LNAME, 
						  s.FNAME, 
						  s.MNAME, 
						  s.SEX, 
						  s.BDAY, 
						  s.HOME_ADD, 
						  s.CONTACT_NO, 
						  c.COURSE_NAME, 
						  sa.PAYMENT
					  FROM tblstudent s
					  JOIN course c ON s.COURSE_ID = c.COURSE_ID
					  LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
					  WHERE s.student_status = 'accepted'
				  ");
				  
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
					  echo '<td>' . $result->PAYMENT . '</td>';   // Displaying Payment Status from studentaccount
				  		 
					  echo '<td align="center" > <a title="View Information" href="index.php?view=view&id='.$result->IDNO.'"  class="btn btn-info btn-xs  ">View <span class="fa fa-info-circle fw-fa"></span></a>

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