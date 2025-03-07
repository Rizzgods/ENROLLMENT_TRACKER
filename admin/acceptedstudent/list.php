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
                <h3 class="panel-title">Filter Students</h3>
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
                    
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="sex_filter" style="margin-right: 5px;">Gender:</label>
                        <select name="sex_filter" id="sex_filter" class="form-control">
                            <option value="">All</option>
                            <option value="Male" <?php echo (isset($_GET['sex_filter']) && $_GET['sex_filter'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($_GET['sex_filter']) && $_GET['sex_filter'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="index.php?view=list" class="btn btn-default">Reset</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add this right after the filter panel -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Export Options</h3>
            </div>
            <div class="panel-body">
                <!-- Print Button -->
                <button onclick="printTable()" class="btn btn-primary" style="margin-right: 10px;">
                    <i class="fa fa-print"></i> Print
                </button>
                
                <!-- Export as PDF Button -->
                <button onclick="exportToPDF()" class="btn btn-danger" style="margin-right: 10px;">
                    <i class="fa fa-file-pdf-o"></i> Export as PDF
                </button>
                
                <!-- Export as CSV Button -->
                <button onclick="exportToCSV()" class="btn btn-success" style="margin-right: 10px;">
                    <i class="fa fa-file-excel-o"></i> Export as CSV
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Active Filters Display -->
<?php if (isset($_GET['course_filter']) || isset($_GET['sex_filter'])): ?>
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
            
            // Display sex filter
            if (isset($_GET['sex_filter']) && !empty($_GET['sex_filter'])) {
                echo (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) ? " | " : "";
                echo " Gender: " . $_GET['sex_filter'];
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<form action="controller.php?action=delete" Method="POST">  
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
                    <th>Course</th>
                    <th>Payment</th>
                    <th width="14%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Build the query with filters
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
                    sa.PAYMENT
                FROM tblstudent s
                JOIN course c ON s.COURSE_ID = c.COURSE_ID
                LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
                INNER JOIN student st ON s.IDNO = st.id  -- Ensures only students in 'student' table are included
                WHERE 1=1";  // 1=1 allows us to add WHERE conditions with AND

                // Apply course filter if selected
                if (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
                    $sql .= " AND s.COURSE_ID = '" . $_GET['course_filter'] . "'";
                }
                
                // Apply sex filter if selected
                if (isset($_GET['sex_filter']) && !empty($_GET['sex_filter'])) {
                    $sql .= " AND s.SEX = '" . $_GET['sex_filter'] . "'";
                }
                
                // Execute the filtered query
                $mydb->setQuery($sql);
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
                    echo '<td>' . $result->PAYMENT . '</td>';
                    echo '<td align="center"><a title="View Information" href="index.php?view=view&id='.$result->IDNO.'" class="btn btn-info btn-xs">View <span class="fa fa-info-circle fw-fa"></span></a></td>';
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
            <strong>Total Records:</strong> <?php echo count($cur); ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
// Print table - modified to remove action column
function printTable() {
    // Get the original table
    const originalTable = document.getElementById('dash-table');
    
    // Create a clone of the table to modify
    const tableToPrint = originalTable.cloneNode(true);
    
    // Remove the action column from header and all rows
    const headerRow = tableToPrint.querySelector('thead tr');
    const actionColumnIndex = headerRow.cells.length - 1; // Last column is Action
    
    // Remove Action header
    headerRow.deleteCell(actionColumnIndex);
    
    // Remove Action column from all rows
    const rows = tableToPrint.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.deleteCell(actionColumnIndex);
    });
    
    let filters = '';
    
    // Include active filters in the print
    if (document.querySelector('.alert-info')) {
        filters = document.querySelector('.alert-info').outerHTML;
    }
    
    let title = '<h2>Accepted Students List</h2>';
    let date = '<p>Date: ' + new Date().toLocaleDateString() + '</p>';
    let institution = '<p>Bestlink College of the Philippines</p>';
    
    // Create print content with the modified table
    let printContent = '<div style="padding: 20px;">' + 
                       title + 
                       institution + 
                       date + 
                       filters + 
                       tableToPrint.outerHTML + 
                       '</div>';
    
    // Save original body content
    let originalContents = document.body.innerHTML;
    
    // Replace body content with print content
    document.body.innerHTML = printContent;
    
    // Add a print stylesheet
    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            body { font-family: Arial, sans-serif; }
            h2 { text-align: center; }
            p { text-align: center; margin: 5px 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #f2f2f2; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; padding: 12px; margin-bottom: 16px; }
        }
    `;
    document.head.appendChild(style);
    
    // Print the document
    window.print();
    
    // Restore original content
    document.body.innerHTML = originalContents;
    
    // Reload page after printing to restore functionality
    setTimeout(function() {
        location.reload();
    }, 1000);
}

// Export as PDF
function exportToPDF() {
    // Initialize jsPDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Add institution name at the top
    doc.setFontSize(16);
    doc.setFont(undefined, 'bold');
    doc.text('BESTLINK COLLEGE OF THE PHILIPPINES', doc.internal.pageSize.width / 2, 15, { align: 'center' });
    
    // Add title
    doc.setFontSize(14);
    doc.setFont(undefined, 'normal');
    doc.text('Accepted Students List', doc.internal.pageSize.width / 2, 25, { align: 'center' });
    
    // Add filters info if present
    let yPos = 35;
    if (document.querySelector('.alert-info')) {
        let filterText = document.querySelector('.alert-info').innerText.replace('Active Filters:', '').trim();
        doc.setFontSize(11);
        doc.text('Filters: ' + filterText, 14, yPos);
        yPos += 8;
    }
    
    // Add date
    doc.setFontSize(11);
    doc.text('Date: ' + new Date().toLocaleDateString(), 14, yPos);
    
    // Add the table
    const table = document.getElementById('dash-table');
    window.jspdf.jsPDF = jsPDF;
    
    // Extract table data
    let headers = [];
    let data = [];
    
    // Get headers (excluding the Action column)
    for (let i = 0; i < table.tHead.rows[0].cells.length - 1; i++) {
        headers.push(table.tHead.rows[0].cells[i].textContent);
    }
    
    // Get data (excluding the Action column)
    for (let i = 0; i < table.tBodies[0].rows.length; i++) {
        let row = [];
        for (let j = 0; j < table.tBodies[0].rows[i].cells.length - 1; j++) {
            row.push(table.tBodies[0].rows[i].cells[j].textContent.trim());
        }
        data.push(row);
    }
    
    // Add the table to the PDF
    doc.autoTable({
        head: [headers],
        body: data,
        startY: yPos + 5,
        margin: { top: 20 },
        styles: { overflow: 'linebreak' },
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        theme: 'striped'
    });
    
    // Add total records count
    const finalY = doc.lastAutoTable.finalY || 70;
    doc.setFontSize(11);
    doc.text(`Total Records: ${data.length}`, 14, finalY + 10);
    
    // Save the PDF
    doc.save('bcp_accepted_students_list.pdf');
}

// Export as CSV
function exportToCSV() {
    // Get the table
    const table = document.getElementById('dash-table');
    
    // Extract table data
    let headers = [];
    let data = [];
    
    // Add institution name as first row in CSV
    let institutionRow = '"BESTLINK COLLEGE OF THE PHILIPPINES"';
    let titleRow = '"Accepted Students List"';
    let dateRow = '"Date: ' + new Date().toLocaleDateString() + '"';
    let spacerRow = '';
    
    // Get filters if present
    let filterRow = '';
    if (document.querySelector('.alert-info')) {
        let filterText = document.querySelector('.alert-info').innerText.replace('Active Filters:', '').trim();
        filterRow = '"Filters: ' + filterText + '"';
    }
    
    // Get headers (excluding the Action column)
    for (let i = 0; i < table.tHead.rows[0].cells.length - 1; i++) {
        headers.push('"' + table.tHead.rows[0].cells[i].textContent + '"');
    }
    
    // Get data (excluding the Action column)
    for (let i = 0; i < table.tBodies[0].rows.length; i++) {
        let row = [];
        for (let j = 0; j < table.tBodies[0].rows[i].cells.length - 1; j++) {
            // Wrap text in quotes to handle commas in the data
            row.push('"' + table.tBodies[0].rows[i].cells[j].textContent.trim().replace(/"/g, '""') + '"');
        }
        data.push(row.join(','));
    }
    
    // Add total records row
    let totalRow = '"Total Records: ' + data.length + '"';
    
    // Combine all components
    let csv = institutionRow + '\n' + titleRow + '\n' + dateRow + '\n';
    
    // Add filter row if present
    if (filterRow) {
        csv += filterRow + '\n';
    }
    
    // Add spacer and then the actual data
    csv += spacerRow + '\n' + headers.join(',') + '\n' + data.join('\n') + '\n\n' + totalRow;
    
    // Create a download link
    let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement("a");
    
    // Create a URL for the blob
    let url = URL.createObjectURL(blob);
    
    // Add filters to filename if present
    let filenameExtra = '';
    if (document.querySelector('.alert-info')) {
        filenameExtra = '_filtered';
    }
    
    // Set link properties
    link.setAttribute("href", url);
    link.setAttribute("download", `bcp_accepted_students${filenameExtra}_${new Date().toISOString().slice(0,10)}.csv`);
    link.style.visibility = 'hidden';
    
    // Append link to document
    document.body.appendChild(link);
    
    // Click the link to start download
    link.click();
    
    // Remove the link
    document.body.removeChild(link);
}
</script>

</div> <!---End of container-->