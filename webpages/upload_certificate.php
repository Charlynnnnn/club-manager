<?php
session_start();
require_once '../classes/events.class.php';
require_once '../classes/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian') {
    header('location: ./index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventID = $_POST['eventID'];
    $userID = $_POST['userID'];
    $ecName = $_POST['ecName'];
    $ecImage = $_POST['certificateImageData'];

    // Decode the image data
    $ecImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $ecImage));

    // Generate a unique file name
    $fileName = 'certificate_' . uniqid() . '.png';

    // Save the image to the server
    file_put_contents('../certificate_images/' . $fileName, $ecImage);

    // Prepare to save to the database
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare('INSERT INTO event_certificate (eventID, userID, ecName, ecImage) VALUES (:eventID, :userID, :ecName, :ecImage)');
    $stmt->bindParam(':eventID', $eventID);
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':ecName', $ecName);
    $stmt->bindParam(':ecImage', $fileName);

    if ($stmt->execute()) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            Swal.fire({
                title: "Certificate uploaded successfully!",
                text: "",
                icon: "success"
            }).then(() => {
                window.location.href = "event-overview.php?librarianID=' . $_SESSION['librarianID'] . '&eventID=' . $eventID . '";
            });
        </script>';
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            Swal.fire({
                title: "Oops!",
                text: "Failed to upload certificate.",
                icon: "error"
            }).then(() => {
                window.history.back();
            });
        </script>';
    }
}
?>
