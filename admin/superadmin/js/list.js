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

        new Chart(document.getElementById('totalStudentsChart'), {
            type: 'bar',
            data: {
                labels: [data.day], // Use daily label
                datasets: [{
                    label: 'Daily Enrollees',
                    data: [data.dailyCount], // Use daily enrollment count
                    backgroundColor: 'rgba(255, 99, 132, 0.5)', // Different color for daily chart
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1 } // Smaller step size for daily data
                    } 
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