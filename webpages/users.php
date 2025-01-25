<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ./index.php');
    }
    function monthNumberToName($monthNumber) {
    $monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    return $monthNames[$monthNumber - 1];
}

// Function to convert time to AM/PM format
function convertToAMPM($time) {
    return date("g:i A", strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Users';
  $activePage = 'users';
  require_once('../include/head.php');
?>

<body>

    <div class="main">
        <div class="row">
            <?php
                require_once('../include/nav-panel.php');
            ?>

            <div class="col-12 col-md-8 col-lg-9">
                
                <div class="row pt-3 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                        <div class="heading-name">
                            <p class="pt-3">Users</p>
                        </div>
                    </div>
                    <div class="row ps-2">
                        <div class="dropdown">
                            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Download
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                                <li><a class="dropdown-item" href="#" onclick="downloadAsPdf()">Download as PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="downloadAsExcel()">Download as Excel</a></li>
                            </ul>
                            <button class="btn download-btn" type="button" id="reportButton">Generate Report</button>
                        </div>
                        <div class="table-responsive">
                            <?php
                            require_once '../classes/user.class.php';
                            $user = new User();
                            $userArray = $user->show();
                            ?>
                            <table id="kt_datatable_both_scrolls" class="table table-striped table-row-bordered gy-5 gs-7 user-table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th class="min-w-200px">Name</th>
                                        <th class="min-w-150px">Gender</th>
                                        <th class="min-w-300px">Date of Birth</th>
                                        <th class="min-w-200px">Age</th>
                                        <th class="min-w-100px">Contact Number</th>
                                        <th class="min-w-100px">School/Office</th>
                                        <th class="min-w-100px">Civil Status</th>
                                        <th class="min-w-100px">Address</th>
                                        <th class="min-w-150px">Created At</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                        <?php
                            if ($userArray) {
                                foreach ($userArray as $item) {
                        ?>
                                    <tr>
                                        <td><?= $item['userFirstName'] . ' ' . $item['userMiddleName'] . ' ' . $item['userLastName'] ?></td>
                                        <td><?= $item['userGender'] ?></td>
                                        <td><?= !empty($item['userBirthdate']) ? date("F j, Y", strtotime($item['userBirthdate'])) : ''; ?></td>
                                        <td><?= $item['userAge'] ?></td>
                                        <td><?= $item['userContactNo'] ?></td>
                                        <td><?= $item['userSchoolOffice'] ?></td>
                                        <td><?= $item['userCivilStatus'] ?></td>
                                        <td>
                                            <?php 
                                            $address_parts = [];
                                            if ($item['userStreetName']) {
                                                $address_parts[] = $item['userStreetName'];
                                            }
                                            if ($item['userBarangay']) {
                                                $address_parts[] = $item['userBarangay'];
                                            }
                                            if ($item['userCity']) {
                                                $address_parts[] = $item['userCity'];
                                            }
                                            if ($item['userProvince']) {
                                                $address_parts[] = $item['userProvince'];
                                            }
                                            if ($item['userZipCode']) {
                                                $address_parts[] = $item['userZipCode'];
                                            }
                                            echo implode(', ', $address_parts);
                                            ?>
                                        </td>
                                        <td><?= !empty($item['userCreatedAt']) ? date("F j, Y", strtotime($item['userCreatedAt'])) : ''; ?></td>
                                    </tr>
                        <?php
                                }
                            }
                        ?>
                                </tbody>
                            </table>
                        </div>
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
        doc.autoTable({html: '#kt_datatable_both_scrolls'});
        doc.save('users.pdf');
    }

    function downloadAsExcel() {
        const table = document.getElementById('kt_datatable_both_scrolls');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'users.xlsx');
    }

    function createChart(type, data, options) {
        return new Promise((resolve) => {
            const canvas = document.createElement("canvas");
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
                                    size: 30
                                }
                            }
                        },
                        tooltip: {
                            titleFont: {
                                size: 30
                            },
                            bodyFont: {
                                size: 30
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: type === 'pie' ? '' : 'AGE',
                                font: {
                                    size: 30
                                }
                            },
                            ticks: {
                                font: {
                                    size: 30
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 30
                                }
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: type === 'pie' ? 'Gender Distribution' : 'Age Distribution',
                        font: {
                            size: 30
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
        const { totalUsers, genderData, ageData, schoolOfficeData, civilStatusData, genderCounts, ageGroups, schoolOfficeCounts, civilStatusCounts } = generateReportData();

        if (totalUsers === 0) {
            console.error("No Users data available.");
            return;
        }

        window.jsPDF = window.jspdf.jsPDF;
        const doc = new jsPDF();
        doc.setFontSize(22);
        doc.text("User Summary Report", 14, 22);

        // Table with increased text size
        doc.autoTable({
            head: [['Metric', 'Value']],
            body: [
                ['Total Users', totalUsers],
                ['Male', genderCounts.Male || 0],
                ['Female', genderCounts.Female || 0],
                ['Other', genderCounts.Other || 0]
            ],
            startY: 30,
            styles: { fontSize: 14 },
            headStyles: { fontSize: 16 },
        });

        // Generate and add charts
        const pieChartImg = await createChart("pie", genderData);
        const barChartImg = await createChart("bar", ageData);
        const schoolOfficeChartImg = await createChart("pie", schoolOfficeData);
        const civilStatusChartImg = await createChart("pie", civilStatusData);

        doc.addImage(pieChartImg, "PNG", 15, doc.autoTable.previous.finalY + 10, 80, 80);
        doc.addImage(barChartImg, "PNG", 100, doc.autoTable.previous.finalY + 10, 80, 80);
        doc.addImage(schoolOfficeChartImg, "PNG", 15, doc.autoTable.previous.finalY + 90, 80, 80);
        doc.addImage(civilStatusChartImg, "PNG", 100, doc.autoTable.previous.finalY + 90, 80, 80);

        doc.save('user_report.pdf');
    }

    document.getElementById('reportButton').addEventListener('click', generatePDF);

    function generateReportData() {
        const UsersData = [];
        const genderCounts = { Male: 0, Female: 0, Other: 0 };
        const ageGroups = {};
        const schoolOfficeCounts = {};
        const civilStatusCounts = {};

        // Select all rows in the tbody
        const rows = document.querySelectorAll("#kt_datatable_both_scrolls tbody tr");

        if (rows.length === 0) {
            console.error("No rows found in the table.");
            return {
                totalUsers: 0,
                genderData: {},
                ageData: {},
                schoolOfficeData: {},
                civilStatusData: {},
                genderCounts,
                ageGroups,
                schoolOfficeCounts,
                civilStatusCounts
            };
        }

        // Iterate over table rows to gather data
        rows.forEach((row) => {
            const cells = row.querySelectorAll("td");

            if (cells.length < 9) {
                console.error("Row does not have enough cells: ", row);
                return;
            }

            const gender = (cells[1].textContent.trim() || 'Other').toString();
            const age = parseInt(cells[3].textContent.trim(), 10);
            const schoolOffice = cells[5].textContent.trim();
            const civilStatus = cells[6].textContent.trim();

            // Increment gender counts
            if (genderCounts.hasOwnProperty(gender)) {
                genderCounts[gender]++;
            } else {
                genderCounts['Other']++;
            }

            // Increment age group counts
            if (!isNaN(age)) {
                const ageGroup = Math.floor(age / 10) * 10;
                if (ageGroups[ageGroup]) {
                    ageGroups[ageGroup]++;
                } else {
                    ageGroups[ageGroup] = 1;
                }
            }

            // Increment school/office counts
            if (schoolOfficeCounts[schoolOffice]) {
                schoolOfficeCounts[schoolOffice]++;
            } else {
                schoolOfficeCounts[schoolOffice] = 1;
            }

            // Increment civil status counts
            if (civilStatusCounts[civilStatus]) {
                civilStatusCounts[civilStatus]++;
            } else {
                civilStatusCounts[civilStatus] = 1;
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
                label: "Number of Users",
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

        const civilStatusData = {
            labels: Object.keys(civilStatusCounts),
            datasets: [{
                data: Object.values(civilStatusCounts),
                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
            }]
        };

        return {
            totalUsers: rows.length,
            genderData,
            ageData,
            schoolOfficeData,
            civilStatusData,
            genderCounts,
            ageGroups,
            schoolOfficeCounts,
            civilStatusCounts
        };
    }
    </script>
</body>
</html>
