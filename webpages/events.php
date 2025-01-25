<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ./index.php');
    }
    

require_once '../classes/events.class.php';
$event = new Events();
$librarianID = $_SESSION['librarianID'];


$applications = $event->getApplication();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['Approve']) || isset($_POST['Rejected'])) {
        $eventRegistrationID = $_POST['eventRegistrationID'];
        $status = isset($_POST['Approve']) ? 'Approved' : 'Rejected';
        $event->updateApplicationStatus($eventRegistrationID, $status);
        header("Location: ../webpages/events.php?librarianID=" . $_SESSION['librarianID'] . "");
        exit();
    }
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
  $title = 'Events & Announcements';
  $activePage = 'events';
  require_once('../include/head.php');
?>

<body>
    <div class="main">
        <div class="row">
            <?php require_once('../include/nav-panel.php');?>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="row pt-3 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                        <div class="heading-name"><p class="pt-3">Events & Announcements</p></div>


                    </div>
                    <div class="row ps-2">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link tab-label active" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="true">Events</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link tab-label" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button" role="tab" aria-controls="announcements" aria-selected="false">Announcements</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link tab-label" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" type="button" role="tab" aria-controls="application" aria-selected="false">Applications</button></li>
                        </ul>

                        <div class="tab-content" id="myTabContent">

                        <!-- Events Table -->
                        <div class="tab-pane fade show active pt-3" id="events" role="tabpanel" aria-labelledby="events-tab">
                        <div class="container ps-0 mb-2 d-flex justify-content-between">
                        <div class="dropdown">
            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                <li><a class="dropdown-item" href="#" onclick="downloadAsPdf()">Download as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadAsExcel()">Download as Excel</a></li>
            </ul>
                            </div>
                            <div class="d-flex">
                                <div class="form-group col-12 col-lg-12 flex-sm-grow-1 flex-lg-grow-0">
                                    <select name="event-status" id="event-status" class="form-select status-filter">
                                        <option value="">All Status</option>
                                        <option value="Upcoming">Upcoming</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
 


                        <div class="table-responsive">
                        <?php
                        require_once '../classes/events.class.php';
                        require_once '../tools/functions.php';

                        $event = new Events();
                        $librarianID = $_SESSION['librarianID'];
                        $eventsArray = $event->show($librarianID);
                        ?>
                            <table id="kt_datatable_horizontal_scroll" class="table table-striped table-row-bordered gy-5 gs-7">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th class="min-w-200px">Event Name</th>
                                        <th class="min-w-350px description-width">Description</th>
                                        <th class="min-w-300px">Event Facilitators</th>
                                        <th class="min-w-200px">Date Time</th>
                                        <th class="min-w-100px">Participant Limit</th>
                                        <th class="min-w-150px">Venue</th>
                                        <th class="min-w-150px">Collaboration Width</th>
                                        <th class="min-w-150px">Status</th>
                                        <th class="min-w-150px">Created At</th>
                                        <th class="min-w-150px">Updated at</th>
                                        <th scope="col" width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="eventsTableBody">
                                <?php
                                if ($eventsArray) {
                                foreach ($eventsArray as $item) {
                                    $eventFacilitators = $event->getEventFacilitator($item['eventID']);
                                    $eventCollaboration = $event->getEventCollaboration($item['eventID']);
                                ?>
                                    <tr>
                                    <td><?= $item['eventTitle'] ?></td>
                                    <td><?= $item['eventDescription'] ?></td>
                                    <td><?php
                                    foreach ($eventFacilitators as $facilitator) {
                                        $middleInitial = $facilitator['librarianMiddleName'] ? substr($facilitator['librarianMiddleName'], 0, 1) . '.' : '';
                                        echo $facilitator['librarianFirstName'] . ' ' . $middleInitial . ' ' . $facilitator['librarianLastName'] . '<br>';
                                    }?></td>
<td>
    <?= monthNumberToName(date('n', strtotime($item['eventStartDate']))) ?> <?= date('j, Y', strtotime($item['eventStartDate'])) ?> -
    <?= monthNumberToName(date('n', strtotime($item['eventEndDate']))) ?> <?= date('j, Y', strtotime($item['eventEndDate'])) ?><br>
    <?= convertToAMPM($item['eventStartTime']) ?> - <?= convertToAMPM($item['eventEndTime']) ?>
</td>                                    <td><?= $item['eventGuestLimit'] ?></td>
                                    <td>
    <?php 
    $address_parts = [];
    if ($item['eventBuildingName']) {
        $address_parts[] = $item['eventBuildingName'];
    }
    if ($item['eventStreetName']) {
        $address_parts[] = $item['eventStreetName'];
    }
    if ($item['eventBarangay']) {
        $address_parts[] = $item['eventBarangay'];
    }
    if ($item['eventCity']) {
        $address_parts[] = $item['eventCity'];
    }
    if ($item['eventProvince']) {
        $address_parts[] = $item['eventProvince'];
    }
    if ($item['eventZipCode']) {
        $address_parts[] = $item['eventZipCode'];
    }
    echo implode(', ', $address_parts);
    ?>
</td>
                                    <td><?php
                                    foreach ($eventCollaboration as $collab) {
                                        echo $collab['ocName'] . '<br>';
                                    }?></td>
                                    <td><?= $item['eventStatus'] ?></td>
                                    <td>
    <?php 
    if(isset($item['eventCreatedAt']) && strtotime($item['eventCreatedAt']) !== false) {
        echo date("F j, Y", strtotime($item['eventCreatedAt'])); 
    } 
    ?>
</td>
<td>
    <?php 
    if(isset($item['eventUpdatedAt']) && strtotime($item['eventUpdatedAt']) !== false) {
        echo date("F j, Y", strtotime($item['eventUpdatedAt'])); 
    } 
    ?>
</td>
                                    <td class="text-center dropdown">
                                            <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-dots-vertical-rounded action-icon'  aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <li><a class="dropdown-item" href="./event-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $item['eventID']; ?>" data-bs-toggle="modal">
                                                    <div class="d-flex align-items-center">
                                                        <i class='bx bx-info-circle action-icon me-2' aria-hidden="true"></i> Overview
                                                    </div>
                                                </a></li>
                                                <li><a class="dropdown-item" href="./event-form.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo $item['eventID']; ?>">
                                                <div class="d-flex align-items-center">
                                                    <i class='bi bi-file-earmark-plus action-icon me-2' aria-hidden="true"></i> Form
                                                </div></a></li>
                                               
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

                        <!-- Announcements Table -->
                        <div class="tab-pane fade active pt-3" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                        <div class="row">
    <div class="col">
        <div class="dropdown">
            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                <li><a class="dropdown-item" href="#" onclick="downloadAsPdfAnnouncement()">Download as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadAsExcelAnnouncement()">Download as Excel</a></li>
            </ul>
        </div>
    </div>
    <div class="col-auto">
        <a href="../webpages/add-announcement.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">
            <button type="button" class="btn request-btn d-flex justify-content-center align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <i class='bx bx-plus-circle action-icon me-2'></i>
                    Add Announcement
                </div>
            </button>
        </a>
    </div>
</div>


                       
                        <div  id="table-container"class="table-responsive">
                        <?php
                        require_once '../classes/eventannouncement.class.php';
                        if ($_SERVER["REQUEST_METHOD"] == "GET") {
                            $eventsannouncement = new EventsAnnouncement();
                            $librarianID = $_SESSION['librarianID'];
                            $eventsannouncementArray = $eventsannouncement->show($librarianID);
                            ?>
<table id="kt_datatable_both_scrolls" class="table table-striped table-row-bordered gy-5 gs-7">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th class="min-w-250px">Announcement Title</th>
                                        <th class="min-w-150px">Description</th>
                                        <th class="min-w-300px">Date</th>
                                        <th class="min-w-200px">Time</th>
                                        <th class="min-w-100px">Created At</th>
                                        <th class="min-w-150px">Updated At</th>
                                        <th scope="col" width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
    if ($eventsannouncementArray) {
        foreach ($eventsannouncementArray as $item) {
?>
                                    <tr>
                                    <td><?= $item['eaTitle'] ?></td>
                                    <td><?= $item['eaDescription'] ?></td>
                                    <td><?= monthNumberToName(date('n', strtotime($item['eaStartDate']))) ?> <?= date('j', strtotime($item['eaStartDate'])) ?>, <?= date('Y', strtotime($item['eaStartDate'])) ?>-<?= monthNumberToName(date('n', strtotime($item['eaEndDate']))) ?> <?= date('j', strtotime($item['eaEndDate'])) ?>, <?= date('Y', strtotime($item['eaEndDate'])) ?></td>
                                    <td><?= convertToAMPM($item['eaStartTime']) ?>- <?= convertToAMPM($item['eaEndTime']) ?></td>
                                    <td>
    <?php echo isset($item['eaCreatedAt']) ? date("F j, Y", strtotime($item['eaCreatedAt'])) . '<br>' . date("g:i A", strtotime($item['eaCreatedAt'])) : ''; ?>
</td>
<td>
    <?php echo isset($item['eaUpdatedAt']) ? date("F j, Y", strtotime($item['eaUpdatedAt'])) . '<br>' . date("g:i A", strtotime($item['eaUpdatedAt'])) : ''; ?>
</td>

                                        <td class="text-center dropdown">
                                            <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-dots-vertical-rounded action-icon'  aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="../webpages/edit-announcement.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventAnnouncementID=<?php echo $item['eventAnnouncementID']; ?>">
                                <div class="d-flex align-items-center">
                                    <i class='bx bx-edit action-icon me-2' aria-hidden="true"></i> Edit
                                </div>
                            </a></li>
                        <li><a class="dropdown-item" href="../webpages/removeannouncement.php?id=<?php echo $item['eventAnnouncementID']; ?>" onclick="return confirm('Are you sure you want to remove announcement?')">
                                <div class="d-flex align-items-center text-danger">
                                    <i class='bx bx-trash action-icon me-2 text-danger' aria-hidden="true"></i> Delete
                                </div>
                            </a></li>
                    </ul>
                                        </td>
                                    </tr>
                                    <?php
        }}
    }
?>
                                </tbody>
                            </table>           
                        </div>
                        </div>
                        <div class="tab-pane fade" id="application" role="tabpanel" aria-labelledby="application-tab">
                     
                        <div class="container ps-0 mb-0 d-flex" style="margin-top:5%; overflow-y: auto; max-height: 70vh;"> <!-- Make the container scrollable -->
    <div class="row row-cols-1 row-cols-md-3" style="justify-content: space-between;"> <!-- Adjust the number of columns as needed -->
        <?php foreach ($applications as $application) {
            $userFirstName = htmlspecialchars($application['userFirstName']);
            $userMiddleName = htmlspecialchars($application['userMiddleName']);
            $userLastName = htmlspecialchars($application['userLastName']);
            $userUsername = htmlspecialchars($application['userUserName']);
            $userEmail = htmlspecialchars($application['userEmail']);
            $erCreatedAt = date('F j, Y', strtotime($application['erCreatedAt'])); // Format the application date
            $erStatus = $application['erStatus'];
            $background_color = ($erStatus == 'Approved') ? 'lightgreen' : (($erStatus == 'Rejected') ? 'lightcoral' : 'white'); // Change background color based on status
        ?>
        <div class="col mb-3" style="margin-right: 20px; flex: 1 0 calc(33.333% - 20px); max-width: calc(33.333% - 20px);"> <!-- Set each column to be 1/3 of the row -->
        <a href="../webpages/event-application-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventRegistrationID=<?php echo $application['eventRegistrationID']; ?>" style="text-decoration: none; color: inherit;"> <!-- Set hover color to white -->                <div class="card" style="background-color: <?php echo $background_color; ?>; transition: transform 0.3s ease, border-color 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                    <div class="card-body">
                        <p class="card-text text-end" style="margin-bottom: 5px;"><small><?php echo date('M d, Y h:ia', strtotime($application['erCreatedAt'])); ?></small></p> <!-- Display application date and time -->
                        <h5 class="card-title text-center">
                            <?php 
                                // Display user name with or without username
                                echo $userUsername ? "$userFirstName $userMiddleName $userLastName ($userUsername)" : "$userFirstName $userMiddleName $userLastName"; 
                            ?>
                        </h5>
                        <p class="card-text text-center" style="margin-bottom: 5px; color: <?php echo $erStatus != 'Pending' ? 'black' : 'black'; ?>"><?php echo "Email: $userEmail"; ?></p> <!-- Add space between username and email -->
                        <p class="card-text text-center">Description: Sent event registration.</p>
                        <form method="post">
                            <input type='hidden' name='eventRegistrationID' value='<?php echo $application['eventRegistrationID']; ?>'>
                            <input type="hidden" name="clubID" value="<?php echo $clubID; ?>">
                            <div class="text-center"> <!-- Center-align the buttons -->
                                <?php if ($erStatus == 'Pending') { ?>
                                    <button type="submit" name="Approve" class="btn btn-success mx-2">Approve</button>
                                    <button type="submit" name="Rejected" class="btn btn-danger mx-2">Decline</button>
                                <?php } else { ?>
                                    <span class="text-dark"><?php echo $erStatus == 'Approved' ? 'Approved!' : 'Rejected!'; ?></span>
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


    <?php require_once('../include/js.php'); ?>
    <script>
    function downloadAsPdfEvents() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_horizontal_scroll'});
        doc.save('events.pdf');
    }

    function downloadAsExcelEvents() {
        const table = document.getElementById('kt_datatable_horizontal_scroll');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'events.xlsx');
    }
</script>
<script>
    function downloadAsPdfAnnouncement() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_both_scrolls'});
        doc.save('announcement.pdf');
    }

    function downloadAsExcelAnnouncement() {
        const table = document.getElementById('kt_datatable_both_scrolls');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'announcement.xlsx');
    }
    
</script>

</body>