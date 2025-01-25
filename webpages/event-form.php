<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian') {
    header('location: ./index.php');
    exit(); 
}
    require_once '../classes/events.class.php';

if (isset($_GET['eventID'])) {
    $events = new Events();
    $record = $events->fetch($_GET['eventID']);
    $events->eventID = $record['eventID'];
    $eventID = $_GET['eventID'];
    $existingQuestions = $events->fetchQuestions($eventID);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    // Loop through submitted questions
    foreach ($_POST['erQuestion'] as $key => $newQuestion) {
        $questionID = isset($_POST['questionIDs'][$key]) ? $_POST['questionIDs'][$key] : null;
        // If question ID is not empty, update the question
        if (!empty($questionID)) {
            $events->updateQuestion($questionID, $newQuestion);
        } else {
            // Add new question
            // First, add event to event_regform if not already added
            $events->addEventToForm($eventID);
            // Get eventRegistrationFormID of the newly added event
            $eventRegistrationFormID = $events->getEventRegistrationFormID($eventID);
            // Add new question to event_regquestion
            $events->addNewQuestion($eventRegistrationFormID, $newQuestion);
        }
    }
    header("Location: ../webpages/event-form.php?librarianID=" . $_SESSION['librarianID'] . "&eventID=$eventID");
        exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Event Form';
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
                                <a href="./events.php?librarianID=<?php echo $_SESSION['librarianID']; ?>" class="d-flex align-items-center">
                                    <i class='bx bx-arrow-back pe-3 back-icon'></i>
                                    <span class="back-text">Back</span>
                                </a>
                            </button>
                            <p class="pt-3">Event Form</p>
                        </div>
                    </div>
                    <div class="row ps-2">
                        <div class="row club-overview-details-container">
                            <div class="col-12 col-lg-12">
                                <div class="scrollable-container" style="max-height: 450px; overflow-y: auto;">
                                    <form method="post" action="">
                                        <?php foreach ($existingQuestions as $question): ?>
                                            <div class="mb-2 position-relative">
                                                <a href="../webpages/delete_event-question.php?eventRegQuestionID=<?php echo $question['eventRegQuestionID']; ?>&eventID=<?php echo $eventID; ?>" class="btn btn-outline-danger border-2 position-absolute top-0 end-0"><span>X</span></a>
                                                <textarea class="form-control" name="erQuestion[]" required><?php echo htmlspecialchars($question['erQuestion']); ?></textarea>
                                                <input type="hidden" name="questionIDs[]" value="<?php echo $question['eventRegQuestionID']; ?>">
                                            </div>
                                        <?php endforeach; ?>
                                        <div id="extraFields"></div>
                                        <div class="mb-2 text-center">
                                            <button type="button" id="addFieldsButton" class="btn btn-outline-danger border-2" style="background-color: white; border-color: black; border-style: dashed;"><i class="fa fa-plus text-danger"></i></button>
                                        </div>
                                        <button type="submit" name="save" class="btn btn-primary mt-4 mb-3" id="addEventFormButton" style="width: 80px;">Save</button>
                                    </form>
                                </div>
                                <script>
                                    document.getElementById("addFieldsButton").addEventListener("click", function() {
                                        var extraFields = document.getElementById("extraFields");
                                        var newFieldHTML = '<div class="mb-2 position-relative"><a href="#" class="btn btn-outline-danger border-2 position-absolute top-0 end-0"><span>X</span></a><textarea class="form-control" name="erQuestion[]" rows="3" required></textarea></div>';
                                        extraFields.insertAdjacentHTML("beforeend", newFieldHTML);
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('../include/js2.php'); ?>
</body>
