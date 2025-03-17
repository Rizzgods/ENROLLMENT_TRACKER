

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            
            <!-- Sidebar (Counts) -->
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Dashboard</h2>
                    </div>
                    <div class="panel-body">
                        <div class="panel panel-primary text-center" data-target="AdminTable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Admins</h3>
                            </div>
                            <div class="panel-body">
                                <p class="lead"><?php echo $admin_count; ?></p>
                            </div>
                        </div>
                        <div class="panel panel-success text-center" data-target="preEnrolleesTable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Pre-Enrollees</h3>
                            </div>
                            <div class="panel-body">
                                <p class="lead"><?php echo $pre_enrollees_count; ?></p>
                            </div>
                        </div>
                        <div class="panel panel-warning text-center" data-target="EnrolleesTable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Enrollees</h3>
                            </div>
                            <div class="panel-body">
                                <p class="lead"><?php echo $enrollees_count; ?></p>
                            </div>
                        </div>
                        <div class="panel panel-danger text-center" data-target="StudentTable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Students</h3>
                            </div>
                            <div class="panel-body">
                                <p class="lead"><?php echo $students_count; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Statistics (Charts) -->
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Enrollment Statistics</h2>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="courseChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="weeklyEnrollmentChart"></canvas>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="panel panel-primary">
                                    <div class="panel-body">
                                        <h3><span id="acceptedPercentage" class="text-success"></span>% Accepted</h3>
                                        <p>Enrollment Percentage</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <canvas id="paymentStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const countBoxes = document.querySelectorAll("[data-target]");
    const tables = document.querySelectorAll("table");

    countBoxes.forEach(box => {
        box.addEventListener("click", function () {
            const targetTableId = this.getAttribute("data-target");

            tables.forEach(table => table.classList.add("d-none"));
            document.getElementById(targetTableId).classList.remove("d-none");
        });
    });

    function searchTable(inputId, tableId) {
        let input = document.getElementById(inputId);
        input.addEventListener("input", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll(`#${tableId} tbody tr`);

            rows.forEach(row => {
                let nameCell = row.querySelector("td:nth-child(2)");
                if (nameCell) {
                    let text = nameCell.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                }
            });
        });
    }

    document.querySelectorAll(".cursor-pointer").forEach(card => {
        card.addEventListener("click", function() {
            let targetTableId = this.getAttribute("data-target");

            document.querySelectorAll("table").forEach(table => table.classList.add("d-none"));
            document.querySelectorAll("input[type='text']").forEach(input => input.classList.add("d-none"));

            document.getElementById(targetTableId).classList.remove("d-none");
            let searchInput = document.getElementById(targetTableId.replace("Table", "Search"));
            if (searchInput) {
                searchInput.classList.remove("d-none");
            }
        });
    });

    searchTable("AdminSearch", "AdminTable");
    searchTable("preEnrolleesSearch", "preEnrolleesTable");
    searchTable("EnrolleesSearch", "EnrolleesTable");
    searchTable("StudentSearch", "StudentTable");

    fetch('fetchstatistics.php')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('courseChart'), {
            type: 'bar',
            data: {
                labels: data.courseNames,
                datasets: [{
                    label: 'Students per Course',
                    data: data.courseCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 2 } }
                }
            }
        });

        new Chart(document.getElementById('weeklyEnrollmentChart'), {
            type: 'bar',
            data: {
                labels: [data.week],
                datasets: [{
                    label: 'Weekly Enrollees',
                    data: [data.enrollCount],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { stepSize: 2 } } }
            }
        });

        document.getElementById('acceptedPercentage').innerText = data.accepted;

        new Chart(document.getElementById('paymentStatusChart'), {
            type: 'pie',
            data: {
                labels: ['Paid Students', 'Unpaid Students'],
                datasets: [{
                    label: 'Payment Status',
                    data: [data.paid, data.unpaid],
                    backgroundColor: ['#4CAF50', '#FF5733'],
                    borderColor: ['#388E3C', '#D32F2F'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

    }).catch(error => console.error('Error fetching data:', error));
});
</script>