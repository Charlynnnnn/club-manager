<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ./index.php');
    }
    require_once '../classes/clubs.class.php';
    require_once '../tools/functions.php';

    if(isset($_GET['clubID'])){
        $club =  new Clubs();
        $record = $club->fetch($_GET['clubID']);
        $club->clubID = $record['clubID'];
        $club->clubName = $record['clubName'];
    }
    if(isset($_GET['clubAnnouncementID'])){
        $clubannouncement =  new Clubannouncement();
        $record = $clubannouncement->fetch($_GET['clubAnnouncementID']);
        $clubannouncement->clubAnnouncementID = $record['clubAnnouncementID'];
        $clubannouncement->caTitle = $record['caTitle'];
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
                        <div class="heading-name d-flex">
                        <button class="back-btn me-4">
                           <a href="../webpages/clubs.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-arrow-back pe-3 back-icon'></i>
                                <span class="back-text">Back</span></a>
                            </div>
                        </button>
                            <p class="pt-3"><?php echo $club->clubName; ?> - Announcement</p>
                        </div>

                       
                    </div>
                    
                    <div class="row ps-2">
    <div class="col">
        <div class="dropdown">
            <button class="btn download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                <li><a class="dropdown-item" href="#" onclick="downloadAsPdf()">Download as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="downloadAsExcel()">Download as Excel</a></li>
            </ul>
        </div>
    </div>
    <div class="col-auto">
        <a href="../webpages/add-club-announcement.php?librarianID=<?php echo $_SESSION['librarianID'] ?>&clubID=<?php echo isset($_GET['clubID']) ? $_GET['clubID'] : ''; ?>" class="btn request-btn d-flex justify-content-center align-items-center mb-2" role="button">
            <div class="d-flex align-items-center">
                <i class='bx bx-plus-circle action-icon me-2'></i>
                Add Announcement
            </div>
        </a>
        </div>
        <div class="table-responsive">
                           
                    
                        <?php require_once '../classes/club-announcement.class.php';
                        $clubannouncement = new Announcement();
                        if(isset($_GET['clubID']) && !empty($_GET['clubID'])){
                            $clubID = $_GET['clubID'];
                            $clubannouncementArray = $clubannouncement->show($clubID);
                        }
                        ?>
                       <table id="kt_datatable_horizontal_scroll" class="table table-striped table-row-bordered gy-5 gs-7">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th class="min-w-200px">Announcement Title</th>
                                        <th class="min-w-150px">Description</th>
                                        <th class="min-w-300px">DateTime</th>
                                        <th class="min-w-200px">Priority</th>
                                        <th class="min-w-150px">Created At</th>
                                        <th class="min-w-150px">Updated At</th>
                                        <th scope="col" width="5%">Action</th>
                                    </tr>
                                </thead>
                              
        </thead>
        <tbody id="clubAnnouncementTableBody">
        <?php
        if ($clubannouncementArray) {
            foreach ($clubannouncementArray as $item) {
                ?>
            <tr>
            <td><?= $item['caTitle'] ?></td>
            <td><?= $item['caDescription'] ?></td>
            <td>
    <?= date('M d, Y', strtotime($item['caStartDate'])) ?> - <?= date('M d, Y', strtotime($item['caEndDate'])) ?><br>
    <?= date('h:ia', strtotime($item['caStartTime'])) ?> - <?= date('h:ia', strtotime($item['caEndTime'])) ?>
</td>
            <td><?= $item['caCondition'] ?></td>
            <td><?php echo date("F j, Y", strtotime($item['caCreatedAt'])); ?><br> <?php echo convertToAMPM($item['caCreatedAt']); ?></td>
<td><?php echo date("F j, Y", strtotime($item['caUpdatedAt'])); ?><br> <?php echo convertToAMPM($item['caUpdatedAt']); ?></td>

            <td class="text-center dropdown">
                                            <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-dots-vertical-rounded action-icon'  aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="../webpages/edit-club-announcement.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $item['clubID']; ?>&clubAnnouncementID=<?php echo $item['clubAnnouncementID']; ?>">
                                <div class="d-flex align-items-center">
                                    <i class='bx bx-edit action-icon me-2' aria-hidden="true"></i> Edit
                                </div>
                            </a></li>
                        <li><a class="dropdown-item" href="../webpages/remove-club-announcement.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&clubID=<?php echo $item['clubID']; ?>&clubAnnouncementID=<?php echo $item['clubAnnouncementID']; ?>" onclick="return confirm('Are you sure you want to remove announcement?')">
                                <div class="d-flex align-items-center text-danger">
                                    <i class='bx bx-trash action-icon me-2 text-danger' aria-hidden="true"></i> Delete
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
  
    <?php require_once('../include/js4.php'); ?>
    <script>
    function downloadAsPdf() {
        window.jsPDF = window.jspdf.jsPDF;

        const doc = new jsPDF();
        doc.autoTable({html: '#kt_datatable_horizontal_scroll'});
        doc.save('club-announcement.pdf');
    }

    function downloadAsExcel() {
        const table = document.getElementById('kt_datatable_horizontal_scroll');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'club-announcement.xlsx');
    }
</script>
   

</body>