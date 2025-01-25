<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
    header('location: ./index.php');
}
require_once '../classes/events.class.php';
if(isset($_GET['eventID'])){
    $event =  new Events();
    $eventRecord = $event->fetch($_GET['eventID']);
    $event->eventID = $eventRecord['eventID'];
    $event->eventTitle= $eventRecord['eventTitle'];
    $event->eventStartDate= $eventRecord['eventStartDate'];
    $event->eventEndDate= $eventRecord['eventEndDate'];
    $event->eventStartTime= $eventRecord['eventStartTime'];
    $event->eventEndTime= $eventRecord['eventEndTime'];
    $event->eventBuildingName= $eventRecord['eventBuildingName'];
    $event->eventStreetName= $eventRecord['eventStreetName'];
    $event->eventBarangay= $eventRecord['eventBarangay'];
    $event->eventCity= $eventRecord['eventCity'];
    $event->eventProvince= $eventRecord['eventProvince'];
    $event->eventZipCode= $eventRecord['eventZipCode'];
    $eventFacilitators = $event->getEventFacilitator($_GET['eventID']);
    $event->eventStatus= $eventRecord['eventStatus'];
    $event->eventDescription= $eventRecord['eventDescription'];
    $registrants = $event->getEventRegistrant($_GET['eventID']);
    $participants = $event->getEventParticipant($_GET['eventID']);
    $volunteers = $event->getEventVolunteers($_GET['eventID']);
}
function monthNumberToName($monthNumber) {
    $monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    return $monthNames[$monthNumber - 1];
}
function convertToAMPM($time) {
    return date("g:i A", strtotime($time));
}
?>


<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Event Overview'; // Set the correct title here
  $activePage = 'events';
  require_once('../include/head.php');
?>

<body>
    <div class="main">
        <div class="row">
            <?php require_once('../include/nav-panel.php'); ?>
            <div class="col-12 col-md-7 col-lg-9">          
                <div class="row pt-4 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                            <div class="heading-name d-flex">
                            <button class="back-btn me-4">
                            <a href="../webpages/events.php?librarianID=<?php echo $_SESSION['librarianID']; ?>" class="d-flex align-items-center">
                                    <i class='bx bx-arrow-back pe-3 back-icon'></i>
                                    <span class="back-text">Back</span>
                                </a>
                            </button><p class="pt-3">Event Overview</p></div></div>

                    <div class="row ps-2"> 
                    <div class="col-12 col-md-6 col-lg-6 club-overview-labels mb-4 ps-3">
                        <div class="overflow-auto" style="max-height: 100px;">
                            <h4 class="club-name pb-1"><span class="label-club pe-3">Event Title</span><br><?php echo $event->eventTitle; ?></h4>
                            <h4 class="dateTime pb-1">
                                <span class="label-club pe-3">Date & Time</span><br>
                                <?php echo !empty($event->eventStartDate) ? date("F j, Y", strtotime($event->eventStartDate)) : ''; ?> - <?php echo !empty($event->eventEndDate) ? date("F j, Y", strtotime($event->eventEndDate)) : ''; ?><br>
                                <?php echo !empty($event->eventStartTime) ? convertToAMPM($event->eventStartTime) : ''; ?> - <?php echo !empty($event->eventEndTime) ? convertToAMPM($event->eventEndTime) : ''; ?><br>
                            </h4>
                            <h4 class="venue pb-1"><span class="label-club pe-3">Venue</span><br><?php
                            $venue = '';
                            if (!empty($event->eventBuildingName)) {
                                $venue .= $event->eventBuildingName;
                            } if (!empty($event->eventStreetName)) {
                                if (!empty($venue)) {
                                    $venue .= ', ';
                                }
                                $venue .= $event->eventStreetName;
                            } if (!empty($event->eventBarangay)) {
                                if (!empty($venue)) {
                                    $venue .= ', ';
                                }
                                $venue .= $event->eventBarangay;
                            } if (!empty($event->eventCity)) {
                                if (!empty($venue)) {
                                    $venue .= ', ';
                                }
                                $venue .= $event->eventCity;
                            } if (!empty($event->eventProvince)) {
                                if (!empty($venue)) {
                                    $venue .= ', ';
                                }
                                $venue .= $event->eventProvince;
                            } if (!empty($event->eventZipCode)) {
                                if (!empty($venue)) {
                                    $venue .= ', ';
                                }
                                $venue .= $event->eventZipCode;
                            } echo $venue;
                            ?>
                            </h4>
                            <h4 class="eventFacilitators pb-1"><span class="label-club pe-3">Event Facilitators</span><?php
                                    foreach ($eventFacilitators as $facilitator) {
                                        $middleInitial = $facilitator['librarianMiddleName'] ? substr($facilitator['librarianMiddleName'], 0, 1) . '.' : '';
                                        echo $facilitator['librarianFirstName'] . ' ' . $middleInitial . ' ' . $facilitator['librarianLastName'] . '<br>';
                                    }?></h4>
                            <h4 class="status pb-1"><span class="label-club pe-3">Status</span><?php echo $event->eventStatus; ?></h4>
                        </div>
                    </div>
                    <?php
                        require_once '../classes/events.class.php';
                        require_once '../tools/functions.php';

                        $events = new Events();
                        $librarianID = $_SESSION['librarianID'];
                        $eventsArray = $events->show($librarianID);
                        ?>
                    <div class="col-12 col-md-6 col-lg-6 club-overview-labels">
                        <div class="overflow-auto" style="max-height: 100px;">
                            <h3 class="description-label"><span class="label-club pb-1 pe-3">Description</span></h3>
                            <h4 class="description-club mb-2"><?php echo $event->eventDescription; ?></h4>
                            
                            <h4 class="status pb-1"><span class="label-club pe-3">Collaboration</span></h4>
                            <?php foreach ($eventsArray as $item): ?>
            <?php
            $eventCollaboration = $events->getEventCollaboration($item['eventID']);
            ?>
            <?php foreach ($eventCollaboration as $collab): ?>
                <?php echo $collab['ocName'] . '<br>'; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-label active" id="event-registrants-tab" data-bs-toggle="tab" data-bs-target="#event-registrants" type="button" role="tab" aria-controls="event-registrants" aria-selected="true">Registrants</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-label" id="event-participants-tab" data-bs-toggle="tab" data-bs-target="#event-participants" type="button" role="tab" aria-controls="event-participants" aria-selected="false">Participants</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-label" id="event-volunteers-tab" data-bs-toggle="tab" data-bs-target="#event-volunteers" type="button" role="tab" aria-controls="event-volunteers" aria-selected="false">Volunteers</button>
                        </li>
                    </ul> 

                    <div class="tab-content" id="myTabContent">
                        <!-- Registrants -->
                        <div class="tab-pane fade show active pt-3" id="event-registrants" role="tabpanel" aria-labelledby="event-registrants-tab">
                            <div class="table-responsive mt-2">
                            <div class="dropdown">
            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                <li><a class="dropdown-item" href="#" onclick="downloadAsPdfRegistrant()">Download as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadAsExcelRegistrant()">Download as Excel</a></li>
            </ul>
            <button class="btn download-btn" type="button" id="reportButton">Generate Report</button>
        </div>
                                <table id="kt_datatable_both_scrolls" class="table table-striped table-row-bordered gy-5 gs-7 club-member">
                                    <thead>
                                        <tr class="fw-semibold fs-6 text-gray-800">
                                        <?php $counter = 1;?>
                                            <th class="min-w-50px" id="number-row">#</th> <!-- Add a column for the list numbers -->
                                            <th class="min-w-250px">Name</th>
                                            <th class="min-w-150px">Email Address</th>
                                            <th class="min-w-300px">Contact Number</th>
                                            <th class="min-w-200px">Gender</th>
                                            <th class="min-w-200px">Address</th>
                                            <th class="min-w-200px">Age</th>
                                            <th class="min-w-100px">Date Registered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($registrants as $registrant) {
                                        ?>
                                        <tr>
                                            <td><?= $counter ?></td>
    <td><?php echo $registrant['fullName']; ?></td>
    <td><?php echo $registrant['userEmail']; ?></td>
    <td><?php echo $registrant['userContactNo']; ?></td>
    <td><?php echo $registrant['userGender']; ?></td>
    <td><?php echo $registrant['address']; ?></td>
    <td><?php echo $registrant['userAge']; ?></td>
    <td><?php echo !empty($registrant['dateJoined']) ? date("F j, Y", strtotime($registrant['dateJoined'])) : ''; ?></td>
</tr>

                            <?php
                             $counter++;
                        }
                        ?>
                                    </tbody>
                                </table>
            
                                </div>
                        </div>
                        <!-- Participants -->
                        <div class="tab-pane fade  active pt-3" id="event-participants" role="tabpanel" aria-labelledby="event-participants-tab">
                        <div class="container ps-0 mb-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                <div class="dropdown">
            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                <li><a class="dropdown-item" href="#" onclick="downloadAsPdfParticipant()">Download as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadAsExcelParticipant()">Download as Excel</a></li>
            </ul>
            <button class="btn download-btn" type="button" id="reportButton1">Generate Report</button>               
                                 
                                    <button type="button" class="btn add-btn justify-content-center align-items-center">
    <a href="event-gallery.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $event->eventID; ?>" class="d-flex align-items-center" style="text-decoration: none; color: inherit;">
        <i class='bx bx-photo-album button-action-icon me-2'></i>
        Gallery
    </a>
</button>

<button type="button" class="btn add-btn justify-content-center align-items-center ms-2">
                                    <a href="event-feedbacks.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $event->eventID; ?>" class="d-flex align-items-center" style="text-decoration: none; color: inherit;">

                                            <i class='bx bx-message-alt-detail button-action-icon me-2'></i>
                                            Feedback</a>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive mt-2">
                                <table id="kt_datatable_horizontal_scroll" class="table table-striped table-row-bordered gy-5 gs-7 club-member">
                                    <thead>
                                    
                                        <tr class="fw-semibold fs-6 text-gray-800">
                                            <th class="min-w-250px">Name</th>
                                            <th class="min-w-150px">Email Address</th>
                                            <th class="min-w-300px">Contact Number</th>
                                            <th class="min-w-200px">Gender</th>
                                            <th class="min-w-200px">Address</th>
                                            <th class="min-w-200px">Age</th>
                                            <th class="min-w-100px">Date Participated</th>
                                            <th class="min-w-100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                        // Loop through each member
                        foreach ($participants as $participant) {
                            ?>
                          <tr>
                         
    <td><?php echo $participant['fullName']; ?></td>
    <td><?php echo $participant['userEmail']; ?></td>
    <td><?php echo $participant['userContactNo']; ?></td>
    <td><?php echo $participant['userGender']; ?></td>
    <td><?php echo $participant['address']; ?></td>
    <td><?php echo $participant['userAge']; ?></td>
    <td><?php echo !empty($participant['dateJoined']) ? date("F j, Y", strtotime($participant['dateJoined'])) : ''; ?></td>
    <td>
    <button type="button" class="btn add-btn justify-content-center align-items-center me-2" onclick="window.location.href = 'event-certificate.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $event->eventID; ?>&userID=<?php echo $participant['userID']; ?>';">
        Generate Certificate
    </button>
</td>
</tr>

                            <?php
                         
                        }
                        ?>
                                    </tbody>
                                </table>
            
                                </div>
                        </div>
                  

                       <!-- volunteers -->
                       <div class="tab-pane fade  active pt-3" id="event-volunteers" role="tabpanel" aria-labelledby="event-volunteers-tab">
                       <div class="container ps-0 mb-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                <div class="dropdown">
            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                <li><a class="dropdown-item" href="#" onclick="downloadAsPdfVolunteer()">Download as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadAsExcelVolunteer()">Download as Excel</a></li>
            </ul>
            <button class="btn download-btn" type="button" id="reportButton2">Generate Report</button>

                                    <button type="button" class="btn add-btn justify-content-center align-items-center">
    <a href="event-gallery.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $event->eventID; ?>" class="d-flex align-items-center" style="text-decoration: none; color: inherit;">
        <i class='bx bx-photo-album button-action-icon me-2'></i>
        Gallery
    </a>
</button>

<button type="button" class="btn add-btn justify-content-center align-items-center ms-2">
                                    <a href="event-feedbacks.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $event->eventID; ?>" class="d-flex align-items-center" style="text-decoration: none; color: inherit;">

                                            <i class='bx bx-message-alt-detail button-action-icon me-2'></i>
                                            Feedback</a>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive mt-2">
                                
                                <table id="kt_datatables_horizontal_scroll" class="table table-striped table-row-bordered gy-5 gs-7 club-member">
                                    <thead>
                                 
                                        <tr class="fw-semibold fs-6 text-gray-800">
                                            <th class="min-w-250px">Name</th>
                                            <th class="min-w-150px">Email Address</th>
                                            <th class="min-w-300px">Contact Number</th>
                                            <th class="min-w-200px">Gender</th>
                                            <th class="min-w-200px">Address</th>
                                            <th class="min-w-200px">Age</th>
                                            <th class="min-w-100px">Date Participated</th>
                                            <th class="min-w-100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($volunteers)) { ?>
                                    <tr>
                                        <td colspan="7">No data available in table</td>
                                    </tr>
                                <?php } else { ?>
                                    <?php foreach ($volunteers as $volunteer) { ?>
                                        <tr>
                                            <td><?php echo $volunteer['fullName']; ?></td>
                                            <td><?php echo $volunteer['userEmail']; ?></td>
                                            <td><?php echo $volunteer['userContactNo']; ?></td>
                                            <td><?php echo $volunteer['userGender']; ?></td>
                                            <td><?php echo $volunteer['address']; ?></td>
                                            <td><?php echo $volunteer['userAge']; ?></td>
                                            <td><?php echo $volunteer['dateJoined'] ? date("F j, Y", strtotime($volunteer['dateJoined'])) : ''; ?></td>
                                            <td>
    <button type="button" class="btn add-btn justify-content-center align-items-center me-2" onclick="window.location.href = 'event-certificate-volunteer.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $event->eventID; ?>&userID=<?php echo $participant['userID']; ?>';">
        Generate Certificate
    </button>
</td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                    </tbody>
                                </table>
                            
                   

                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <?php require_once('../include/js3.php'); ?>
    <script>
    function downloadAsPdfRegistrant() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_both_scrolls'});
        doc.save('registrant.pdf');
    }

    function downloadAsExcelRegistrant() {
        const table = document.getElementById('kt_datatable_both_scrolls');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'registrant.xlsx');
    }
    function downloadAsPdfParticipant() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_horizontal_scroll'});
        doc.save('participant.pdf');
    }

    function downloadAsExcelParticipant() {
        const table = document.getElementById('kt_datatable_horizontal_scroll');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'participant.xlsx');
    }
    function downloadAsPdfVolunteer() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatables_horizontal_scroll'});
        doc.save('volunteer.pdf');
    }

    function downloadAsExcelVolunteer() {
        const table = document.getElementById('kt_datatables_horizontal_scroll');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'volunteer.xlsx');
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.18/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function generateReportData() {
    const registrantsData = [];
    const genderCounts = { Male: 0, Female: 0, Other: 0 };
    const ageGroups = {};

    // Select all rows in the tbody
    const rows = document.querySelectorAll("#kt_datatable_both_scrolls tbody tr");
    
    if (rows.length === 0) {
        console.error("No rows found in the table.");
        return {
            totalRegistrants: 0,
            genderData: {},
            ageData: {},
            genderCounts,
            ageGroups
        };
    }

    // Iterate over table rows to gather data
    rows.forEach((row) => {
        const cells = row.querySelectorAll("td");

        if (cells.length < 8) {
            console.error("Row does not have enough cells: ", row);
            return;
        }

        const age = parseInt(cells[6].textContent.trim(), 10);
        const gender = cells[4].textContent.trim() || 'Other'; // Default to 'Other' if gender is missing

        // Increment gender counts
        if (genderCounts[gender] !== undefined) {
            genderCounts[gender]++;
        } else {
            genderCounts['Other']++;
        }

        // Increment age group counts
        const ageGroup = Math.floor(age / 10) * 10;
        if (ageGroups[ageGroup]) {
            ageGroups[ageGroup]++;
        } else {
            ageGroups[ageGroup] = 1;
        }
    });

    // Prepare data for charts
    const genderData = {
        labels: Object.keys(genderCounts),
        datasets: [{
            data: Object.values(genderCounts),
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
        }]
    };

    const ageLabels = Object.keys(ageGroups).map(age => `${age}-${parseInt(age) + 9}`);
    const ageData = {
        labels: ageLabels,
        datasets: [{
            label: "Number of Registrants",
            data: Object.values(ageGroups),
            backgroundColor: "#FF6384"
        }]
    };

    return {
        totalRegistrants: rows.length,
        genderData,
        ageData,
        genderCounts,
        ageGroups
    };
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
    const { totalRegistrants, genderData, ageData, genderCounts, ageGroups } = generateReportData();
    
    if (totalRegistrants === 0) {
        console.error("No registrants data available.");
        return;
    }
    
    window.jsPDF = window.jspdf.jsPDF;
    const doc = new jsPDF();
    doc.setFontSize(22);
    doc.text("Registrant Summary Report", 14, 22);

    // Table with increased text size
    doc.autoTable({
        head: [['Metric', 'Value']],
        body: [
            ['Total Registrants', totalRegistrants],
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

    doc.addImage(pieChartImg, "PNG", 15, doc.autoTable.previous.finalY + 10, 80, 80);
    doc.addImage(barChartImg, "PNG", 100, doc.autoTable.previous.finalY + 10, 80, 80);

    doc.save('registrant_report.pdf');
}

document.getElementById('reportButton').addEventListener('click', generatePDF);

</script>

<script>
function generateReportData() {
    const partcipantsData = [];
    const genderCounts = { Male: 0, Female: 0, Other: 0 };
    const ageGroups = {};

    // Select all rows in the tbody
    const rows = document.querySelectorAll("#kt_datatable_horizontal_scroll tbody tr");
    
    if (rows.length === 0) {
        console.error("No rows found in the table.");
        return {
            totalParticipants: 0,
            genderData: {},
            ageData: {},
            genderCounts,
            ageGroups
        };
    }

    // Iterate over table rows to gather data
    rows.forEach((row) => {
        const cells = row.querySelectorAll("td");

        if (cells.length < 8) {
            console.error("Row does not have enough cells: ", row);
            return;
        }

        const age = parseInt(cells[5].textContent.trim(), 10);
        const gender = cells[3].textContent.trim() || 'Other'; // Default to 'Other' if gender is missing

        // Increment gender counts
        if (genderCounts[gender] !== undefined) {
            genderCounts[gender]++;
        } else {
            genderCounts['Other']++;
        }

        // Increment age group counts
        const ageGroup = Math.floor(age / 10) * 10;
        if (ageGroups[ageGroup]) {
            ageGroups[ageGroup]++;
        } else {
            ageGroups[ageGroup] = 1;
        }
    });

    // Prepare data for charts
    const genderData = {
        labels: Object.keys(genderCounts),
        datasets: [{
            data: Object.values(genderCounts),
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
        }]
    };

    const ageLabels = Object.keys(ageGroups).map(age => `${age}-${parseInt(age) + 9}`);
    const ageData = {
        labels: ageLabels,
        datasets: [{
            label: "Number of Participants",
            data: Object.values(ageGroups),
            backgroundColor: "#FF6384"
        }]
    };

    return {
        totalParticipants: rows.length,
        genderData,
        ageData,
        genderCounts,
        ageGroups
    };
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
    const { totalParticipants, genderData, ageData, genderCounts, ageGroups } = generateReportData();
    
    if (totalParticipants === 0) {
        console.error("No Participants data available.");
        return;
    }
    
    window.jsPDF = window.jspdf.jsPDF;
    const doc = new jsPDF();
    doc.setFontSize(22);
    doc.text("Participants Summary Report", 14, 22);

    // Table with increased text size
    doc.autoTable({
        head: [['Metric', 'Value']],
        body: [
            ['Total Participants', totalParticipants],
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

    doc.addImage(pieChartImg, "PNG", 15, doc.autoTable.previous.finalY + 10, 80, 80);
    doc.addImage(barChartImg, "PNG", 100, doc.autoTable.previous.finalY + 10, 80, 80);

    doc.save('Participants_report.pdf');
}

document.getElementById('reportButton1').addEventListener('click', generatePDF);
</script>

<script>
function generateReportData() {
    const volunteersData = [];
    const genderCounts = { Male: 0, Female: 0, Other: 0 };
    const ageGroups = {};

    // Select all rows in the tbody
    const rows = document.querySelectorAll("#kt_datatables_horizontal_scroll tbody tr");
    
    if (rows.length === 0) {
        console.error("No rows found in the table.");
        return {
            totalVolunteers: 0,
            genderData: {},
            ageData: {},
            genderCounts,
            ageGroups
        };
    }

    // Iterate over table rows to gather data
    rows.forEach((row) => {
        const cells = row.querySelectorAll("td");

        if (cells.length < 8) {
            console.error("Row does not have enough cells: ", row);
            return;
        }

        const age = parseInt(cells[5].textContent.trim(), 10);
        const gender = cells[3].textContent.trim() || 'Other'; // Default to 'Other' if gender is missing

        // Increment gender counts
        if (genderCounts[gender] !== undefined) {
            genderCounts[gender]++;
        } else {
            genderCounts['Other']++;
        }

        // Increment age group counts
        const ageGroup = Math.floor(age / 10) * 10;
        if (ageGroups[ageGroup]) {
            ageGroups[ageGroup]++;
        } else {
            ageGroups[ageGroup] = 1;
        }
    });

    // Prepare data for charts
    const genderData = {
        labels: Object.keys(genderCounts),
        datasets: [{
            data: Object.values(genderCounts),
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
        }]
    };

    const ageLabels = Object.keys(ageGroups).map(age => `${age}-${parseInt(age) + 9}`);
    const ageData = {
        labels: ageLabels,
        datasets: [{
            label: "Number of Volunteers",
            data: Object.values(ageGroups),
            backgroundColor: "#FF6384"
        }]
    };

    return {
        totalVolunteers: rows.length,
        genderData,
        ageData,
        genderCounts,
        ageGroups
    };
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
    const { totalVolunteers, genderData, ageData, genderCounts, ageGroups } = generateReportData();
    
    if (totalVolunteers === 0) {
        console.error("No Volunteers data available.");
        return;
    }
    
    window.jsPDF = window.jspdf.jsPDF;
    const doc = new jsPDF();
    doc.setFontSize(22);
    doc.text("Volunteers Summary Report", 14, 22);

    // Table with increased text size
    doc.autoTable({
        head: [['Metric', 'Value']],
        body: [
            ['Total Volunteers', totalVolunteers],
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

    doc.addImage(pieChartImg, "PNG", 15, doc.autoTable.previous.finalY + 10, 80, 80);
    doc.addImage(barChartImg, "PNG", 100, doc.autoTable.previous.finalY + 10, 80, 80);

    doc.save('Volunteers_report.pdf');
}

document.getElementById('reportButton2').addEventListener('click', generatePDF);
</script>
</body>