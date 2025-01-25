<?php
require_once '../tools/librarianfunctions.php';
require_once '../classes/club-announcement.class.php';

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian') {
    header('location: ./index.php');

}
require_once '../classes/clubs.class.php';
    if(isset($_GET['clubID'])){
        $club =  new Clubs();
        $record = $club->fetch($_GET['clubID']);
        $club->clubID = $record['clubID'];
}
if (isset($_POST['save'])) {
    $announcement = new Announcement();
    $clubID = $club->clubID;
    $announcement->clubID = $clubID;
    $announcement->caTitle = htmlentities($_POST['caTitle']);
    $announcement->caDescription = htmlentities($_POST['caDescription']);
    $announcement->caStartDate = htmlentities($_POST['caStartDate']);
    $announcement->caStartTime = htmlentities($_POST['caStartTime']);
    $announcement->caEndDate = htmlentities($_POST['caEndDate']);
    $announcement->caEndTime = htmlentities($_POST['caEndTime']);
    $announcement->caCondition = htmlentities($_POST['caCondition']);

    // Validate input fields
    if (validate_field($announcement->caTitle) &&
    validate_field($announcement->caStartDate) &&
        validate_field($announcement->caStartTime)) {

        if ($announcement->add()) {
            header("Location: ../webpages/club-announcement.php?librarianID=" . $_SESSION['librarianID'] . "&clubID=" . (isset($_GET['clubID']) ? $_GET['clubID'] : ''));
            exit; // Stop script execution after redirect
        } else {
            echo 'An error occurred while adding the announcement in the database.';
        }
    }
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
            <?php
                require_once('../include/nav-panel.php');
            ?>

            <div class="col-12 col-md-8 col-lg-9">
                
                <!-- Add Announcement Modal -->
                <div class="container mt-4">
                    <div class="header-modal d-flex justify-content-between">
                        <h5 class="modal-title mt-4 ms-1" id="addAnnouncementModalLabel">Add Announcement</h5>
                      
                    </div>
                    <div class=" modal-body mt-2">
                    <form method="post" action="">
                        <div class="row d-flex justify-content-center my-1">
                            <div class="input-group flex-column mb-3">
                                <label for="caTitle" class="label">Announcement Title</label>
                                <input type="text" name="caTitle" id="caTitle" class="input-1" placeholder="Enter Announcement Title" required value="<?php if(isset($_POST['caTitle'])) { echo $_POST['caTitle']; } ?>">
                                <?php
                                    if(isset($_POST['caTitle']) && !validate_field($_POST['caTitle'])){
                                ?>
                                        <p class="text-danger my-1">Title is required</p>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center my-1">
                            <div class="input-group flex-column mb-3">
                                <label for="caDescription" class="label">Description</label>
                                <input type="text" id="caDescription" name="caDescription" class="input-1" rows="4" cols="50" placeholder="Write brief description" value="<?php if(isset($_POST['caDescription'])) { echo $_POST['caDescription']; } ?>">
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center my-1">
                            <div class="input-group flex-column mb-3">
                                <label for="eventDate" class="label">Date</label>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="caStartDate" class="label-2">Start Date</label>
                                        <input type="date" name="caStartDate" id="caStartDate" class="input-1 col-lg-12" placeholder="From" required value="<?php if(isset($_POST['caStartDate'])) { echo $_POST['caStartDate']; } ?>">
                                        <?php
                                            if(isset($_POST['caStartDate']) && !validate_field($_POST['caStartDate'])){
                                        ?>
                                                <p class="text-danger my-1">Start date is required</p>
                                        <?php
                                            }
                                        ?>
                                    </div>

                                    <div class="col-6">
                                        <label for="caEndDate" class="label-2">End Date</label>
                                        <input type="date" name="caEndDate" id="caEndDate" class="input-1 col-lg-12" placeholder="To" required value="<?php if(isset($_POST['caEndDate'])) { echo $_POST['caEndDate']; } ?>">
                                    </div>
                                </div>
                                <div></div>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center my-1">
                            <div class="input-group flex-column mb-3">
                                <label for="eventTime" class="label">Time</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="caStartTime" class="label-2">Start Time</label>
                                        <input type="time" name="caStartTime" id="caStartTime" class="input-1 col-lg-12" placeholder="From" value="<?php if(isset($_POST['caStartTime'])) { echo $_POST['caStartTime']; } ?>">
                                    </div>

                                    <div class="col-6">
                                        <label for="caEndTime" class="label-2">End Time</label>
                                        <input type="time" name="caEndTime" id="caEndTime" class="input-1 col-lg-12" placeholder="To" value="<?php if(isset($_POST['caEndTime'])) { echo $_POST['caEndTime']; } ?>">
                                    </div>
                                </div>
                                <div></div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center my-1">
                                        <div class="input-group flex-column mb-3">
                                        <label for="description" class="label mb-2">Priority</label>
                                            <div class="d-flex justify-content-center">
                                                <div class="radio-inputs">
                                                    <label class="radio">
                                                        <input type="radio" name="caCondition" value="Required" checked="">
    
                                                        <span class="name">Required</span>
                                                    </label>
                                                    <label class="radio">
                                                        <input type="radio" name="caCondition" value="Optional">
                                                        <span class="name">Optional</span>
                                                    </label>
                                                        
                                                    <label class="radio">
                                                        <input type="radio" name="caCondition" value="Urgent">
                                                        <span class="name">Urgent</span>
                                                    </label>
                                                    <p id="condition_error" class="modal-error text-danger my-1"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                        <div class="modal-action-btn d-flex justify-content-end">
                            <button type="button" class="btn cancel-btn mb-4 me-4" onclick="window.history.back();" aria-label="Close">Cancel</button>
                            <button type="submit" name="save" class="btn request-btn-2 mb-3 me-4" data-bs-dismiss="modal">Done</button>
                        </div>
                    </form>
                    </div>
                    
                    </div>
                </div>
                </div>


    <?php require_once('../include/js.php'); ?>

</body>