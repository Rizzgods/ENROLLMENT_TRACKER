document.addEventListener("DOMContentLoaded", function () {
    const countBoxes = document.querySelectorAll(".grid div[data-target]");
    const tables = document.querySelectorAll("table");

    countBoxes.forEach(box => {
        box.addEventListener("click", function () {
            const targetTableId = this.getAttribute("data-target");

            // Hide all tables
            tables.forEach(table => table.classList.add("hidden"));

            // Show the clicked table
            document.getElementById(targetTableId).classList.remove("hidden");
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    function searchTable(inputId, tableId) {
        let input = document.getElementById(inputId);
        input.addEventListener("input", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll(`#${tableId} tbody tr`);

            rows.forEach(row => {
                let nameCell = row.querySelector("td:nth-child(2)"); // Target Name column (2nd column)
                if (nameCell) {
                    let text = nameCell.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                }
            });
        });
    }

    // Table Switching Logic
    document.querySelectorAll(".cursor-pointer").forEach(card => {
        card.addEventListener("click", function() {
            let targetTableId = this.getAttribute("data-target");

            // Hide all tables and search bars
            document.querySelectorAll("table").forEach(table => table.classList.add("hidden"));
            document.querySelectorAll("input[type='text']").forEach(input => input.classList.add("hidden"));

            // Show the clicked table and its search bar
            document.getElementById(targetTableId).classList.remove("hidden");
            let searchInput = document.getElementById(targetTableId.replace("Table", "Search"));
            if (searchInput) {
                searchInput.classList.remove("hidden");
            }
        });
    });

    // Apply search function to each table
    searchTable("AdminSearch", "AdminTable");
    searchTable("preEnrolleesSearch", "preEnrolleesTable");
    searchTable("EnrolleesSearch", "EnrolleesTable");
    searchTable("StudentSearch", "StudentTable");
});


fetch('fetchstatistics.php')
.then(response => response.json())
.then(data => {
    // Render Course Chart
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2
                    }
                }
            }
        }
    });

    // Render Enrollment Chart
    const ctx = document.getElementById('weeklyEnrollmentChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [data.week], // Shows current week
            datasets: [{
                label: 'Weekly Enrollees',
                data: [data.enrollCount], // Shows count for current week
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true,
                    ticks: {
                        stepSize: 2
                    }
                 },
                
            }
        }
    });

})
.catch(error => console.error('Error fetching data:', error));


fetch('fetchstatistics.php') // Fetch enrollment status data
    .then(response => response.json())
    .then(data => {
        document.getElementById('acceptedPercentage').innerText = data.accepted;
    })
    .catch(error => console.error('Error fetching enrollment status:', error));



    fetch('fetchstatistics.php')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie', // Change to 'bar' if you prefer a bar chart
            data: {
                labels: ['Paid Students', 'Unpaid Students'],
                datasets: [{
                    label: 'Payment Status',
                    data: [data.paid, data.unpaid],
                    backgroundColor: ['#4CAF50', '#FF5733'], // Green for paid, red for unpaid
                    borderColor: ['#388E3C', '#D32F2F'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error fetching payment data:', error));  