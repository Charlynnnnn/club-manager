<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ../index.php');
    }
    function monthNumberToName($monthNumber) {
        $monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        return $monthNames[$monthNumber - 1];
    }
    function convertToAMPM($time) {
        return date("g:i A", strtotime($time));
    }
?><!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Attendance';
  $activePage = 'attendance';
  require_once('../include/head.php');
?>

<body>
<div id="consoleOutput" style="display: none;"></div>
    <div class="main">
        <div class="row">
            <?php require_once('../include/nav-panel.php'); ?>

            <div class="col-12 col-md-8 col-lg-9">
                
                <div class="row pt-3 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                        <div class="heading-name">
                            <p class="pt-3">Attendance</p>
                        </div>
                    </div>

                    <div class="container ps-0 mb-2 d-flex justify-content-between">
                        <div class="row ps-2">
                            <div class="dropdown">
                                <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bxs-download action-icon-3 me-2'></i>Download
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                                    <li><a class="dropdown-item" href="#" onclick="downloadAsPdf()">Download as PDF</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="downloadAsExcel()">Download as Excel</a></li>
                                </ul>
                                <button class="btn download-btn" type="button" id="reportButton">Generate Report</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <?php
                            require_once '../classes/user.class.php';
                            $attendance = new User();
                            $attendanceArray = $attendance->show();
                        ?>
                        <table id="kt_datatable_both_scrolls" class="table table-striped table-row-bordered gy-5 gs-7 user-table kt-datatable">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800">
                                    <th class="min-w-200px">Date</th>
                                    <th class="min-w-150px">Time</th>
                                    <th class="min-w-300px">Name</th>
                                    <th class="min-w-200px">Gender</th>
                                    <th class="min-w-100px">Age</th>
                                    <th class="min-w-100px">School/Office</th>
                                    <th class="min-w-100px">Address</th>
                                    <th class="min-w-100px">Contact No.</th>
                                    <th class="min-w-100px">Purpose</th>
                                    <th class="min-w-100px">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                            <?php
                                if ($attendanceArray) {
                                    foreach ($attendanceArray as $item) {
                                        $getAttendancedate = $attendance->getAttendanceDetails($item['userID']);
                                        foreach ($getAttendancedate as $attendancedate) {
                            ?>
                            <tr>
                                <td><?= $attendancedate['dateEntered'] ? monthNumberToName(date('n', strtotime($attendancedate['dateEntered']))) . ' ' . date('j, Y', strtotime($attendancedate['dateEntered'])) : ''; ?></td>
                                <td><?= $attendancedate['timeEntered'] ? convertToAMPM($attendancedate['timeEntered']) : ''; ?></td>
                                <td><?= $attendancedate['userFirstName'] . ' ' . $attendancedate['userMiddleName'] . ' ' . $attendancedate['userLastName']; ?></td>
                                <td><?= $attendancedate['userGender']; ?></td> 
                                <td><?= $attendancedate['userAge']; ?></td> 
                                <td><?= $attendancedate['userSchoolOffice']; ?></td> 
                                <td><?php 
    $addressParts = [];
    if ($attendancedate['userStreetName']) {
        $addressParts[] = $attendancedate['userStreetName'];
    }
    if ($attendancedate['userBarangay']) {
        $addressParts[] = $attendancedate['userBarangay'];
    }
    if ($attendancedate['userCity']) {
        $addressParts[] = $attendancedate['userCity'];
    }
    echo implode(', ', $addressParts);
?></td>
                                <td><?php echo $attendancedate['userContactNo']; ?></td>
                                <td><?php echo $attendancedate['purpose']; ?></td>
                                <td><?php echo $attendancedate['acFirstName'] . ' ' . $attendancedate['acMiddleName'] . ' ' . $attendancedate['acLastName']; ?></td>
                            </tr>
                            <?php
                                        }
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
                        <canvas id="barChart" width="800" height="400"></canvas>
                        <canvas id="pieChart" width="800" height="400" style="display: none;"></canvas>
                        <div id="chartInterpretation"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('../include/js.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.18/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.6/xlsx.full.min.js"></script>
    <script>
    function downloadAsPdf() {
        window.jsPDF = window.jspdf.jsPDF;
        const doc = new jsPDF();
        doc.autoTable({ html: '#kt_datatable_both_scrolls' });
        doc.save('attendance.pdf');
    }

    function downloadAsExcel() {
        const table = document.getElementById('kt_datatable_both_scrolls');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'attendance.xlsx');
    }

    function createChart(type, data, options) {
        return new Promise((resolve) => {
            const canvas = document.createElement("canvas");
            canvas.style.display = 'none';
            document.body.appendChild(canvas);
            const ctx = canvas.getContext("2d");
            const chart = new Chart(ctx, {
                type: type,
                data: data,
                options: {
                    ...options,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 20
                                }
                            }
                        },
                        tooltip: {
                            titleFont: {
                                size: 20
                            },
                            bodyFont: {
                                size: 20
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: type === 'pie' ? '' : 'AGE',
                                font: {
                                    size: 20
                                }
                            },
                            ticks: {
                                font: {
                                    size: 20
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 20
                                }
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: type === 'pie' ? 'Distribution' : 'Categories Distribution',
                        font: {
                            size: 20
                        }
                    }
                }
            });

            setTimeout(() => {
                const imgData = canvas.toDataURL("image/png");
                document.body.removeChild(canvas);
                resolve(imgData);
            }, 500);
        });
    }

    async function generatePDF() {
        const { totalRecords, genderData, ageData, schoolOfficeData, purposeData, genderCounts, ageGroups, schoolOfficeCounts, purposeCounts } = generateReportData();

        if (totalRecords === 0) {
            console.error("No records data available.");
            return;
        }

        window.jsPDF = window.jspdf.jsPDF;
        const doc = new jsPDF();
        doc.setFontSize(22);
        doc.text("Attendance Summary Report", 14, 22);

        // Table with increased text size
        doc.autoTable({
            head: [['Metric', 'Value']],
            body: [
                ['Total Records', totalRecords],
                ['Male', genderCounts.Male || 0],
                ['Female', genderCounts.Female || 0],
                ['Other', genderCounts.Other || 0]
            ],
            startY: 30,
            styles: { fontSize: 14 },
        });

        // Gender Distribution Chart
        const genderImgData = await createChart('pie', genderData, {});
        doc.addPage();
        doc.text("Gender Distribution", 14, 22);
        doc.addImage(genderImgData, 'PNG', 14, 30, 180, 100);

        // Age Distribution Chart
        const ageImgData = await createChart('bar', ageData, {});
        doc.addPage();
        doc.text("Age Distribution", 14, 22);
        doc.addImage(ageImgData, 'PNG', 14, 30, 180, 100);

        // School/Office Distribution Chart
        const schoolOfficeImgData = await createChart('pie', schoolOfficeData, {});
        doc.addPage();
        doc.text("School/Office Distribution", 14, 22);
        doc.addImage(schoolOfficeImgData, 'PNG', 14, 30, 180, 100);

        // Purpose Distribution Chart
        const purposeImgData = await createChart('pie', purposeData, {});
        doc.addPage();
        doc.text("Purpose Distribution", 14, 22);
        doc.addImage(purposeImgData, 'PNG', 14, 30, 180, 100);

        doc.save('attendance-report.pdf');
    }

    function generateReportData() {
        const rows = document.querySelectorAll("#kt_datatable_both_scrolls tbody tr");
        const genderCounts = { Male: 0, Female: 0, Other: 0 };
        const ageGroups = {};
        const schoolOfficeCounts = {};
        const purposeCounts = {};

        rows.forEach((row) => {
            const cells = row.querySelectorAll("td");
            if (cells.length < 9) return;

            const gender = (cells[3].textContent.trim() || 'Other').toString();
            const age = parseInt(cells[4].textContent.trim(), 10);
            const schoolOffice = cells[5].textContent.trim();
            const purpose = cells[8].textContent.trim();

            if (genderCounts.hasOwnProperty(gender)) {
                genderCounts[gender]++;
            } else {
                genderCounts['Other']++;
            }

            if (!isNaN(age)) {
                const ageGroup = Math.floor(age / 10) * 10;
                if (ageGroups[ageGroup]) {
                    ageGroups[ageGroup]++;
                } else {
                    ageGroups[ageGroup] = 1;
                }
            }

            if (schoolOfficeCounts[schoolOffice]) {
                schoolOfficeCounts[schoolOffice]++;
            } else {
                schoolOfficeCounts[schoolOffice] = 1;
            }

            if (purposeCounts[purpose]) {
                purposeCounts[purpose]++;
            } else {
                purposeCounts[purpose] = 1;
            }
        });

        // Prepare data for charts
        const genderData = {
            labels: Object.keys(genderCounts),
            datasets: [{
                data: Object.values(genderCounts),
                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"]
            }]
        };

        const ageLabels = Object.keys(ageGroups).map(age => `${age}-${parseInt(age) + 9}`);
        const ageData = {
            labels: ageLabels,
            datasets: [{
                label: "Number of Attendees",
                data: Object.values(ageGroups),
                backgroundColor: "#FF6384"
            }]
        };

        const schoolOfficeData = {
            labels: Object.keys(schoolOfficeCounts),
            datasets: [{
                data: Object.values(schoolOfficeCounts),
                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
            }]
        };

        const purposeData = {
            labels: Object.keys(purposeCounts),
            datasets: [{
                data: Object.values(purposeCounts),
                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
            }]
        };

        return {
            totalRecords: rows.length,
            genderData,
            ageData,
            schoolOfficeData,
            purposeData,
            genderCounts,
            ageGroups,
            schoolOfficeCounts,
            purposeCounts
        };
    }

    document.getElementById('reportButton').addEventListener('click', generatePDF);
    </script>
</body>
</html>

