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
?>
<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Club Overview';
  $clubs = 'active-1';
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
                            <a href="./club-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $_GET['clubID']; ?>" class="d-flex align-items-center">
    <i class='bx bx-arrow-back pe-3 back-icon'></i>
    <span class="back-text">Back</span>
</a>

                            </button>

                                <p class="pt-3">Club Events</p>
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
            
                            </div></div></div>
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <?php
require_once '../classes/events.class.php';
require_once '../tools/functions.php';

$event_club = new Events();
$events = $event_club->fetchClubEvent($_GET['clubID']); 

if ($events) { // Check if events are retrieved
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
          
        </tr>
    </thead>
    <tbody id="eventsTableBody">
    <?php foreach ($events as $event):
        // Fetch event facilitators for the current event
        $facilitators = $event_club->getEventFacilitator($event->eventID);
        // Fetch event collaboration for the current event
        $collaborations = $event_club->getEventCollaboration($event->eventID);
    ?>
    <tr>
        <td><?= $event->eventTitle ?></td>
        <td><?= $event->eventDescription ?></td>
        <td>
    <?php foreach ($facilitators as $index => $facilitator): ?>
        <?= $facilitator['librarianFirstName'] . ' ' . $facilitator['librarianMiddleName'] . ' ' . $facilitator['librarianLastName'] ?>
        <?php if (!empty(trim($facilitator['librarianFirstName'])) || !empty(trim($facilitator['librarianMiddleName'])) || !empty(trim($facilitator['librarianLastName']))): ?>
            <?php if ($index < count($facilitators) - 1): ?>,<?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</td>

        <td>
            <?= date('M d, Y', strtotime($event->eventStartDate)) ?> - <?= date('M d, Y', strtotime($event->eventEndDate)) ?><br>
            <?= date('h:ia', strtotime($event->eventStartTime)) ?> - <?= date('h:ia', strtotime($event->eventEndTime)) ?>
        </td>
        <td><?= $event->eventGuestLimit ?></td>
        <td>
    <?php
    $addressParts = array_filter(array(
        $event->eventBuildingName,
        $event->eventStreetName,
        $event->eventBarangay,
        $event->eventCity,
        $event->eventProvince,
        $event->eventZipCode
    ));

    echo implode(', ', $addressParts);
    ?>
</td>        <td>
    <?php foreach ($collaborations as $index => $collab): ?>
        <?= $collab['ocName'] ?>
        <?php if (!empty(trim($collab['ocName']))): ?>
            <?php if ($index < count($collaborations) - 1): ?>,<?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</td>
        <td><?= $event->eventStatus ?></td>
        <td> <?= date('M d, Y', strtotime($event->eventCreatedAt)) ?></td>
        <td> <?= date('M d, Y', strtotime($event->eventUpdatedAt)) ?></td>
       
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
<?php } ?>

       


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
        doc.autoTable({html: '#kt_datatable_horizontal_scroll'});
        doc.save('clubevent-overview.pdf');
    }

    function downloadAsExcel() {
        const table = document.getElementById('kt_datatable_horizontal_scroll');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'clubevent-overview.xlsx');
    }
</script>

</body>