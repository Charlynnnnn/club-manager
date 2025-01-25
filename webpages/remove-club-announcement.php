<?php
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
if (isset($_GET['clubAnnouncementID'])) {
    $clubAnnouncementID = $_GET['clubAnnouncementID'];

    // Include necessary files and classes
    require_once '../classes/club-announcement.class.php';

    // Create a new instance of the announcement class
    $announcement = new Announcement();

    // Call a method to delete the announcement by ID
    if ($announcement->delete($clubAnnouncementID)) {
        // Announcement deleted successfully, redirect to the announcements page
        header("location: ../webpages/club-announcement.php?librarianID=" . $_SESSION['librarianID'] . "&clubID=" . (isset($_GET['clubID']) ? $_GET['clubID'] : ''));

        exit();
    } else {
        // An error occurred during deletion
        echo 'Error deleting announcement.';
        exit();
    }
} else {
    // Announcement ID is not provided in the URL
    echo 'Invalid request.';
    exit();
}
?>