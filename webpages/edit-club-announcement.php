
<?php
require_once '../tools/librarianfunctions.php';
require_once '../classes/club-announcement.class.php';
require_once '../classes/clubs.class.php';

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian') {
    header('location: ./index.php');

}
if(isset($_GET['clubID'])){
    $club =  new Clubs();
    $record = $club->fetch($_GET['clubID']);
    $club->clubID = $record['clubID'];
}
$announcement = new Announcement();

if (isset($_GET['clubAnnouncementID'])) {
    $record = $announcement->fetch($_GET['clubAnnouncementID']);
    $announcement->clubAnnouncementID = $record['clubAnnouncementID'];
    $announcement->caTitle = $record['caTitle'];
    $announcement->caDescription = $record['caDescription'];
    $announcement->caCondition = $record['caCondition'];
    $announcement->caStartDate = $record['caStartDate'];
    $announcement->caStartTime = $record['caStartTime'];
    $announcement->caEndDate = $record['caEndDate'];
    $announcement->caEndTime = $record['caEndTime'];
}
if (isset($_POST['save'])) {
    $clubID = $club->clubID;
    $announcement->clubID = $clubID;
    $announcement->clubAnnouncementID = $_GET['clubAnnouncementID'];
    $announcement->caTitle = htmlentities($_POST['caTitle']);
    $announcement->caDescription = htmlentities($_POST['caDescription']);
    $announcement->caCondition = htmlentities($_POST['caCondition']);
    $announcement->caStartDate = htmlentities($_POST['caStartDate']); // Corrected assignment
    $announcement->caStartTime = htmlentities($_POST['caStartTime']);
    $announcement->caEndDate = htmlentities($_POST['caEndDate']); // Corrected assignment
    $announcement->caEndTime = htmlentities($_POST['caEndTime']);

    if (validate_field($announcement->caTitle) &&
        validate_field($announcement->caStartDate) &&
        validate_field($announcement->caStartTime)) {

        if ($announcement->edit()) {
            header("location: ../webpages/club-announcement.php?librarianID=" . $_SESSION['librarianID'] . "&clubID=" . (isset($_GET['clubID']) ? $_GET['clubID'] : ''));
        } else {
            echo 'An error occurred while updating the announcement in the database.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Clubs & Announcements';
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
                
                <!-- Edit Announcement Modal -->
                <div class="container mt-4">
                    <div class="header-modal d-flex justify-content-between">
                        <h5 class="modal-title mt-4 ms-4" id="editAnnouncementModalLabel">Edit Announcement</h5>
                        
                    </div>
                    <div class="modal-body mt-2">
                    <form action="" method="post">
                        <div class="row d-flex justify-content-center my-1">
                            <div class="input-group flex-column mb-3">
                                <label for="caTitle" class="label">Announcement Title</label>
                                <input type="text" name="caTitle" id="caTitle" class="input-1" required value="<?php if(isset($_POST['caTitle'])) { echo $_POST['caTitle']; }else if(isset($announcement->caTitle)) { echo $announcement->caTitle; } ?>">
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
                                <input type="text" id="caDescription" name="caDescription" class="input-1" rows="4" cols="50" value="<?php if(isset($_POST['caDescription'])){ echo $_POST['caDescription']; }else if(isset($announcement->caDescription)) { echo $announcement->caDescription; } ?>">
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center my-1">
                            <div class="input-group flex-column mb-3">
                                <label for="eventDate" class="label">Date</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="caStartDate" class="label-2">Start Date</label>
                                        <input type="date" name="caStartDate" id="caStartDate" class="input-1 col-lg-12" placeholder="From" required value="<?php if(isset($_POST['caStartDate'])) { echo $_POST['caStartDate']; }else if(isset($announcement->caStartDate)) { echo $announcement->caStartDate; } ?>">
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
                                        <input type="date" name="caEndDate" id="caEndDate" class="input-1 col-lg-12" placeholder="To" required value="<?php if(isset($_POST['caEndDate'])) { echo $_POST['caEndDate']; }else if(isset($announcement->caEndDate)) { echo $announcement->caEndDate; } ?>">
                                        <?php
                                            if(isset($_POST['caEndDate']) && !validate_field($_POST['caEndDate'])){
                                        ?>
                                                <p class="text-danger my-1">End date is required</p>
                                        <?php
                                            }
                                        ?>
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
                                        <input type="time" name="caStartTime" id="caStartTime" class="input-1 col-lg-12" placeholder="From" value="<?php if(isset($_POST['caStartTime'])) { echo $_POST['caStartTime']; }else if(isset($announcement->caStartTime)) { echo $announcement->caStartTime; } ?>">
                                    </div>

                                    <div class="col-6">
                                        <label for="caEndTime" class="label-2">End Time</label>
                                        <input type="time" name="caEndTime" id="caEndTime" class="input-1 col-lg-12" placeholder="To" value="<?php if(isset($_POST['caEndTime'])) { echo $_POST['caEndTime']; }else if(isset($announcement->caEndTime)) { echo $announcement->caEndTime; } ?>">
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
                                            <input type="radio" name="caCondition" value="Required" <?php if(isset($_POST['caCondition']) && $_POST['caCondition'] == 'Required') echo 'checked'; else if(isset($announcement->caCondition) && $announcement->caCondition == 'Required') echo 'checked'; ?>>
                                            <span class="name">Required</span>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="caCondition" value="Optional" <?php if(isset($_POST['caCondition']) && $_POST['caCondition'] == 'Optional') echo 'checked'; else if(isset($announcement->caCondition) && $announcement->caCondition == 'Optional') echo 'checked'; ?>>
                                            <span class="name">Optional</span>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="caCondition" value="Urgent" <?php if(isset($_POST['caCondition']) && $_POST['caCondition'] == 'Urgent') echo 'checked'; else if(isset($announcement->caCondition) && $announcement->caCondition == 'Urgent') echo 'checked'; ?>>
                                            <span class="name">Urgent</span>
                                        </label>
                                        <?php
                                        if(isset($_POST['caCondition']) && !validate_field($_POST['caCondition'])){
                                            ?>
                                            <p id="condition_error" class="modal-error text-danger my-1">Priority is required</p>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-action-btn d-flex justify-content-end">
                            <button type="button" class="btn cancel-btn mb-4 me-4" onclick="window.history.back();" aria-label="Close">Cancel</button>
                            <button type="submit" name="save" class="btn request-btn-2 mb-3 me-4">Update</button>
                        </div>
                    </form>
                    </div>   
                </div>
                </div>
    <?php require_once('../include/js.php'); ?>
</body>