<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['clubFormQuestionID'])) {
    require_once '../classes/clubs.class.php';
    $club = new Clubs();
    $clubFormQuestionID = $_GET['clubFormQuestionID'];
    $_SESSION['clubID'] = $_GET['clubID']; // Set clubID here
    if ($club->delete($clubFormQuestionID)) {
        // Deletion successful
        header("Location: ../webpages/club-form.php?librarianID=" . $_SESSION['librarianID'] . "&clubID=" . $_SESSION['clubID']);
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
