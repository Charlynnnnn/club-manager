<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['eventRegQuestionID'])) {
    require_once '../classes/events.class.php';
    $event = new Events();
    $eventRegQuestionID = $_GET['eventRegQuestionID'];
    $_SESSION['eventID'] = $_GET['eventID']; // Set clubID here
    if ($event->delete($eventRegQuestionID)) {
        // Deletion successful
        header("Location: ../webpages/event-form.php?librarianID=" . $_SESSION['librarianID'] . "&eventID=" . $_SESSION['eventID']);
        exit();
    } else {
        // Error in deletion
        echo 'Error deleting question.';
        exit();
    }
} else {
    // Invalid request
    echo 'Invalid request.';
    exit();
}
?>
