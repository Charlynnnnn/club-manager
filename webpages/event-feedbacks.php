<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
    header('location: ./index.php');
}

require_once '../classes/events.class.php';
$event = new Events();
$librarianID = $_SESSION['librarianID'];

require_once '../classes/event_feedback.class.php';
$feedback = new EventFeedback();
$feedbackData = $feedback->getFeedbackWithDetails();
?>

<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Feedbacks';
$activePage = 'events';
require_once('../include/head.php');
?>

<style>
    .card {
        transition: transform 0.6s ease, box-shadow 0.6s ease;
        height: 100%; /* Ensure all cards have the same height */
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .feedback-text {
        font-weight: bold;
        font-size: 18px; /* Increase font size for feedback text */
    }

    .user-details {
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .user-image {
        width: 40px;
        height: 40px;
        border-radius: 50%; /* Make the user image a circle */
        margin-right: 10px;
    }
</style>

<body>

    <div class="main">
        <div class="row">
            <?php
            require_once('../include/nav-panel.php');
            ?>

            <div class="col-12 col-md-8 col-lg-9">

                <div class="container mt-4">
                    <div class="header-modal d-flex align-items-center">
                        <a href="../webpages/event-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo isset($_GET['eventID']) ? $_GET['eventID'] : ''; ?>" class="d-flex align-items-center">
                            <i class='bx bx-arrow-back pe-3 back-icon'></i>
                            <span class="back-text"></span>
                        </a>
                        <h2 class="modal-title " id="addAnnouncementModalLabel">User Feedback</h2>
                    </div>

                    <!-- Feedback Cards -->
                    <div class="row mt-4">
                        <?php if (empty($feedbackData)) { ?>
                            <div class="col">
                                <p>No feedback available.</p>
                            </div>
                        <?php } else { ?>
                            <?php foreach ($feedbackData as $feedbackItem) { ?>
                                <div class="col-md-4 mb-3"> <!-- Adjusted to display 3 cards per row -->
                                    <div class="card h-100"> <!-- Added 'h-100' to ensure cards have the same height -->
                                        <div class="card-body">
                                            <h5 class="card-title feedback-text"><?php echo $feedbackItem['feedback']; ?></h5> <!-- Added 'feedback-text' class for styling -->
                                            <div class="user-details">
    <?php if (!empty($feedbackItem['userImage']) && file_exists($feedbackItem['userImage'])) { ?>
        <img src="<?php echo $feedbackItem['userImage']; ?>" alt="User Image" class="user-image">
    <?php } else { ?>
        <img src="../images/user.png" alt="Placeholder Image" class="user-image">
    <?php } ?>
                                                <span class="text-muted"><?php echo $feedbackItem['userFirstName'] . ' ' . $feedbackItem['userLastName']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>

                </div>
            </div>

            <?php require_once('../include/js.php'); ?>

</body>
