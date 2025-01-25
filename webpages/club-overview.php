<?php
   
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ./index.php');
    }
    require_once '../classes/clubs.class.php';
    if(isset($_GET['clubID'])){
        $club =  new Clubs();
        $record = $club->fetch($_GET['clubID']);
        $club->clubID = $record['clubID'];
        $club->clubName = $record['clubName'];
        $club->clubMinAge = $record['clubMinAge'];
        $club->clubMaxAge = $record['clubMaxAge'];
        $club->clubDescription = $record['clubDescription'];
        $clubManagers = $club->getClubManagers($_GET['clubID']);
        $memberCount = $club->getMemberCount($_GET['clubID']);
        $members = $club->getClubMembers($_GET['clubID']);
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
  $title = 'Club Overview';
  $activePage = 'clubs';
  require_once('../include/head.php');
?>

<body>


    <div class="main">
        <div class="row">
            <?php
                require_once('../include/nav-panel.php');
            ?>

            <div class="col-12 col-md-7 col-lg-9">
                
                <div class="row pt-4 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                            <div class="heading-name d-flex">
                            <button class="back-btn me-4">
                                <a href="./clubs.php?librarianID=<?php echo $_SESSION['librarianID']; ?>" class="d-flex align-items-center">
                                    <i class='bx bx-arrow-back pe-3 back-icon'></i>
                                    <span class="back-text">Back</span>
                                </a>
                            </button>

                                <p class="pt-3">Club Overview</p>
                            </div>

                            

                        </div>

                    <div class="row ps-2">
                   
                    <div class="row club-overview-details-container">
                        <div class="col-12 col-md-6 col-lg-5 club-overview-labels mb-4 ps-3">
                            <h4 class="club-name pb-1"><span class="label-club pe-3">Club Name</span><?php echo $club->clubName; ?></h4>
                            <h4 class="members pb-1"><span class="label-club pe-3">Members</span><?php echo $memberCount; ?></h4>
                            <h4 class="ageLimit pb-1"><span class="label-club pe-3">Age Limit</span><?php
if ($club->clubMinAge == 0 && $club->clubMaxAge == 0) {
    echo "No age limit";
} else {
    echo $club->clubMinAge . "-" . $club->clubMaxAge;
}
?>
</h4>
                            <h4 class="clubManager pb-1"><span class="label-club pe-3">Manage By <?php foreach ($clubManagers as $manager) { ?>
                            <?php echo $manager['librarianFirstName'] . ' ' . $manager['librarianLastName']; ?>
                        <?php } ?></h4>
                        </div>

                        <div class="col-12 col-md-6 col-lg-7 club-overview-labels">
                            <h3 class="description-label"><span class="label-club pb-1 pe-3">Description</span></h3>
                            <h4 class="description-club"><?php echo $club->clubDescription; ?></h4>
                        </div>
                    </div>
                    <div class="container ps-0 mb-2 d-flex justify-content-between">
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
             <a href="../webpages/add-event.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $_GET['clubID']; ?>">
    <button type="button" class="btn add-btn justify-content-center align-items-center me-2">
        <div class="d-flex align-items-center">
            <i class='bx bx-plus-circle button-action-icon me-2'></i>
            Add Event
        </div>
    </button>
</a>                   </div></div></div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table id="kt_datatable_both_scrolls" class="table table-striped table-row-bordered gy-5 gs-7 club-member">
    <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="min-w-10px" id="number-row">#</th> <!-- Add a column for the list numbers -->
            <th class="min-w-150px">Name</th>
            <th class="min-w-150px">Email Address</th>
            <th class="min-w-150px">Contact Number</th>
            <th class="min-w-200px">Gender</th>
            <th class="min-w-200px">Address</th>
            <th class="min-w-100px">Date Joined</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Initialize counter outside the loop
        $counter = 1;
        
        // Loop through each member
        foreach ($members as $member) {
        ?>
            <tr>
                <td><?= $counter ?></td>
                <td><?php echo $member['fullName']; ?></td>
                <td><?php echo $member['userEmail']; ?></td>
                <td><?php echo $member['userContactNo']; ?></td>
                <td><?php echo $member['userGender']; ?></td>
                <td><?php echo $member['address']; ?></td>
                <td>
                    <?php 
                    if(isset($member['dateJoined']) && strtotime($member['dateJoined']) !== false) {
                        echo date("F j, Y", strtotime($member['dateJoined'])) . '<br> ' . convertToAMPM($member['dateJoined']); 
                    } 
                    ?>
                </td>
            </tr>
        <?php
            // Increment counter inside the loop
            $counter++;
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
    <?php require_once('../include/js2.php'); ?>
    <script>
    function downloadAsPdf() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_both_scrolls'});
        doc.save('club-overview.pdf');
    }

    function downloadAsExcel() {
        const table = document.getElementById('kt_datatable_both_scrolls');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'club-overview.xlsx');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

<script>

    function generateTableReportWithChart() {
    window.jsPDF = window.jspdf.jsPDF;
    
    const doc = new jsPDF();

    // Set document title and styles
    doc.setFontSize(22);
    doc.text("Club Members Report", 14, 22);

    // Extract data from the table
    const table = document.getElementById("kt_datatable_both_scrolls");
    const tableRows = table.querySelectorAll("tbody tr");

    const tableData = Array.from(tableRows).map(row => {
        const cells = row.querySelectorAll("td");
        return Array.from(cells).map(cell => cell.innerText);
    });

    // Generate the table in the PDF
    doc.autoTable({
        head: [['#', 'Name', 'Email Address', 'Contact Number', 'Gender', 'Address', 'Date Joined']],
        body: tableData,
        styles: { fontSize: 12 }, // Adjust the font size for table content
        headStyles: { fontSize: 14 }, // Adjust the font size for table headers
    });

    // Create a canvas element for Chart.js
    const canvas = document.createElement('canvas');
    canvas.id = 'chartCanvas';
    document.body.appendChild(canvas);

    // Prepare data for the chart (e.g., gender distribution)
    const genderCounts = {
        'Male': 0,
        'Female': 0,
        'Other': 0
    };
    tableData.forEach(row => {
        if (row[4] in genderCounts) {
            genderCounts[row[4]] += 1;
        }
    });

    // Generate the chart using Chart.js with increased font sizes
    const chart = new Chart(canvas, {
        type: 'pie',
        data: {
            labels: Object.keys(genderCounts),
            datasets: [{
                label: 'Gender Distribution',
                data: Object.values(genderCounts),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 18 // Increase font size of legend labels
                        }
                    }
                },
                tooltip: {
                    titleFont: {
                        size: 40 // Increase font size of tooltip title
                    },
                    bodyFont: {
                        size: 14 // Increase font size of tooltip body
                    }
                }
            }
        }
    });

    // Convert the chart to an image and add it to the PDF
    setTimeout(() => {
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', 14, doc.lastAutoTable.finalY + 20, 180, 90); // Adjust positioning and size as needed

        // Save the PDF
        doc.save('club-member-report.pdf');

        // Clean up by removing the canvas from the DOM
        document.body.removeChild(canvas);
    }, 1000);  // Wait for the chart to be rendered
}


    document.getElementById('reportButton').addEventListener('click', generateTableReportWithChart);
</script>

</body>