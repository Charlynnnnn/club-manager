<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ./index.php');
    }
    require_once '../classes/clubs.class.php';
    $clubs = new Clubs();
    $librarianID = $_SESSION['librarianID'];
    $applications = $clubs->getApplication($librarianID);
    $clubID = $_GET['clubID'] ?? ''; 
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['Approve']) || isset($_POST['Rejected'])) {
        $clubMembershipID = $_POST['clubMembershipID'];
        $status = isset($_POST['Approve']) ? 'Approved' : 'Rejected';
        $clubs->updateApplicationStatus($clubMembershipID, $status);
        header("Location: ../webpages/clubs.php?librarianID=" . $_SESSION['librarianID']);
        exit();
    }else{   
    }

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
  $title = 'Clubs';
  $activePage = 'clubs';
  require_once('../include/head.php');
?>

<body>
    <div class="main">
        <div class="row">
            <?php require_once('../include/nav-panel.php'); ?>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="row pt-3 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                        <div class="heading-name"><p class="pt-3">My Clubs</p></div>
                      
                    </div>
                    <div class="row ps-2">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link tab-label active" id="clubs-tab" data-bs-toggle="tab" data-bs-target="#clubs" type="button" role="tab" aria-controls="clubs" aria-selected="true">Clubs</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link tab-label" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" type="button" role="tab" aria-controls="application" aria-selected="false">Applications</button></li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active pt-3" id="clubs" role="tabpanel" aria-labelledby="clubs-tab">
                       
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

                        <div class="table-responsive">
                            <?php
                            require_once '../classes/clubs.class.php';
                            $clubs = new Clubs();
                            $librarianID = $_SESSION['librarianID'];
                            
                            $clubsArray = $clubs->show($librarianID);
                            ?>
                      
                            <table id="kt_datatable_horizontal_scroll" class="table table-striped table-row-bordered gy-5 gs-7">
                            <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th class="min-w-200px">Club Name</th>
                                        <th class="min-w-150px">Description</th>
                                        <th class="min-w-300px">Club Manager</th>
                                        <th class="min-w-200px">Age Range</th>
                                        <th class="min-w-100px">No. of Members</th>
                                        <th class="min-w-150px">Created At</th>
                                        <th class="min-w-150px">Updated At</th>
                                        <th scope="col" width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="clubsTableBody">
                                <?php
                                $clubManagersData = [];
                                $ageRangeData = [];
                                if ($clubsArray) {
                                    foreach ($clubsArray as $item) {
                                        $clubManagers = $clubs->getClubManagers($item['clubID']);
                                ?>
                                        <tr>
                                        <td><?= $item['clubName'] ?></td>
                                        <td><?= $item['clubDescription'] ?></td>
                                        <td>
                                        <?php
                                        foreach ($clubManagers as $manager) {
                                            $middleInitial = $manager['librarianMiddleName'] ? substr($manager['librarianMiddleName'], 0, 1) . '.' : '';
                                            echo $manager['librarianFirstName'] . ' ' . $middleInitial . ' ' . $manager['librarianLastName'] . '<br>';
                                        
                                        }
                                        ?>
                                        </td>
                                        <td>
                                        <?php
                                        if ($item['clubMinAge'] == 0 && $item['clubMaxAge'] == 0) {
                                            echo 'No age limit';
                                        } else {
                                            echo $item['clubMinAge'] . '- ' . $item['clubMaxAge'];
                                        }
                                        ?>
                                        </td>
                                        <td><?= $item['members'] ?></td>
                                        <td><?php echo date("F j, Y", strtotime($item['clubCreatedAt'])); ?><br> <?php echo convertToAMPM($item['clubCreatedAt']); ?></td>
                                        <td><?php echo date("F j, Y", strtotime($item['clubUpdatedAt'])); ?> <br><?php echo convertToAMPM($item['clubUpdatedAt']); ?></td>

                                        <td class="text-center dropdown">
                                            <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-dots-vertical-rounded action-icon'  aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <li><a class="dropdown-item" href="./club-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $item['clubID']; ?>" data-bs-toggle="modal">
                                                    <div class="d-flex align-items-center">
                                                        <i class='bx bx-info-circle action-icon me-2' aria-hidden="true"></i> Overview
                                                    </div>
                                                </a></li>
                                                <li><a class="dropdown-item" href="./club-announcement.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $item['clubID']; ?>">
                                                    <div class="d-flex align-items-center">
                                                        <i class='bx bxs-megaphone action-icon me-2 ' aria-hidden="true"></i> Announcement
                                                    </div>
                                                </a></li>
                                                <li><a class="dropdown-item" href="./club-form.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $item['clubID']; ?>">
                                                    <div class="d-flex align-items-center">
                                                    <i class='bi bi-file-earmark-plus action-icon me-2' aria-hidden="true"></i> Form
                                                    </div>
                                                </a></li>
                                            </ul>
                                        </td>
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
        <div class="tab-pane fade" id="application" role="tabpanel" aria-labelledby="application-tab">
            <div class="container ps-0 mb-0 d-flex" style="margin-top:5%; overflow-y: auto; max-height: 70vh;">
                <div class="row row-cols-1 row-cols-md-3">
                    <?php foreach ($applications as $application) {
                        $userFirstName = htmlspecialchars($application['userFirstName']);
                        $userMiddleName = htmlspecialchars($application['userMiddleName']);
                        $userLastName = htmlspecialchars($application['userLastName']);
                        $userUsername = htmlspecialchars($application['userUserName']);
                        $userEmail = htmlspecialchars($application['userEmail']);
                        $cmCreatedAt = date('F j, Y', strtotime($application['cmCreatedAt'])); 
                        $cmStatus = $application['cmStatus'];
                        $background_color = ($cmStatus == 'Approved') ? 'lightgreen' : (($cmStatus == 'Rejected') ? 'lightcoral' : 'white');
                        ?>
                        <div class="col mb-3">
                            <a href="../webpages/club-application-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $clubID; ?>&clubMembershipID=<?php echo $application['clubMembershipID']; ?>" style="text-decoration: none; color: inherit;"> <!-- Set hover color to white -->
                                <div class="card" style="background-color: <?php echo $background_color; ?>; transition: transform 0.3s ease, border-color 0.3s ease; cursor: pointer; margin-bottom: 20px;" onmouseover="this.style.transform='translateY(-5px)'; " onmouseout="this.style.transform='translateY(0)'; ">
                                    <div class="card-body">
                                        <p class="card-text text-end"><small><?php echo date('M d, Y h:ia', strtotime($application['cmCreatedAt'])); ?></small></p>
                                        <h5 class="card-title text-center">
                                            <?php 
                                            echo $userUsername ? "$userFirstName $userMiddleName $userLastName ($userUsername)" : "$userFirstName $userMiddleName $userLastName"; 
                                            ?></h5>
        <p class="card-text text-center" style="margin-bottom: 5px; color: <?php echo $cmStatus != 'Pending' ? 'black' : 'black'; ?>"><?php echo "Email: $userEmail"; ?></p> <!-- Add space between username and email -->
        <p class="card-text text-center">Description: Sent club membership application.</p>
        <form method="post">
            <input type='hidden' name='clubMembershipID' value='<?php echo $application['clubMembershipID']; ?>'>
            <input type="hidden" name="clubID" value="<?php echo $clubID; ?>">
            <div class="text-center">
                <?php if ($cmStatus == 'Pending') { ?>
                    <button type="submit" name="Approve" class="btn btn-success mx-2">Approve</button>
                    <button type="submit" name="Rejected" class="btn btn-danger mx-2">Decline</button>
                <?php } else { ?>
                    <span class="text-dark"><?php echo $cmStatus == 'Approved' ? 'Approved!' : 'Rejected!'; ?></span>
                <?php } ?>
            </div>
        </form>
    </div>
</div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('../include/js4.php'); ?>
 <script>
    function downloadAsPdf() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_horizontal_scroll'});
        doc.save('club.pdf');
    }

    function downloadAsExcel() {
        const table = document.getElementById('kt_datatable_horizontal_scroll');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'club.xlsx');
    }

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.18/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function generateReportData() {
    const clubsData = [];
    document.querySelectorAll("#kt_datatable_horizontal_scroll tbody tr").forEach((row) => {
        const cells = row.querySelectorAll("td");
        const clubManagers = cells[2].innerHTML.trim().split('<br>').filter(manager => manager !== "");
        const club = {
            name: cells[0].textContent.trim(),
            members: parseInt(cells[4].textContent.trim(), 10),
            managers: clubManagers.length // Accurate counting of managers
        };
        clubsData.push(club);
    });
    return clubsData;
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
                                size: 30 // Increased font size for legend
                            }
                        }
                    },
                    tooltip: {
                        titleFont: {
                            size: 30 // Increased font size for tooltip title
                        },
                        bodyFont: {
                            size: 30 // Increased font size for tooltip body
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 30 // Increased font size for x-axis labels
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 30 // Increased font size for y-axis labels
                            }
                        }
                    }
                },
                title: {
                    display: true,
                    text: type === 'pie' ? 'Club Members Distribution' : 'Club Managers Distribution',
                    font: {
                        size: 30 // Increased font size for the chart title
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

async function generateCharts(doc, clubsData) {
    // Pie chart for club members
    const pieChartData = {
        labels: clubsData.map(club => club.name),
        datasets: [{
            data: clubsData.map(club => club.members),
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"],
        }]
    };

    // Bar chart for club managers
    const barChartData = {
        labels: clubsData.map(club => club.name),
        datasets: [{
            label: "Club Managers",
            data: clubsData.map(club => club.managers),
            backgroundColor: "#FF6384"
        }]
    };

    const pieChartImg = await createChart("pie", pieChartData);
    const barChartImg = await createChart("bar", barChartData);

    doc.addImage(pieChartImg, "PNG", 15, 60, 80, 80);
    doc.addImage(barChartImg, "PNG", 100, 60, 80, 80);
}

async function generatePDF() {
    const clubsData = generateReportData();
    window.jsPDF = window.jspdf.jsPDF;
    const doc = new jsPDF();
    doc.setFontSize(22); // Increased the font size for the title
    doc.text("Club Membership Report", 14, 22);

    // Table with increased text size
    doc.autoTable({
        head: [['Club Name', 'No. of Members', 'No. of Managers']],
        body: clubsData.map(club => [club.name, club.members, club.managers]),
        styles: { fontSize: 14 }, // Increased font size for table content
        headStyles: { fontSize: 16 }, // Increased font size for table headers
    });

    await generateCharts(doc, clubsData);

    doc.save('club_report.pdf');
}

document.getElementById('reportButton').addEventListener('click', generatePDF);

</script>

</body>