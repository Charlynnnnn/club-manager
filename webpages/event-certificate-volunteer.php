<?php
session_start();
require_once '../classes/events.class.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
    header('location: ./index.php');
    exit();
}

if(isset($_GET['eventID']) && isset($_GET['userID'])){
    $event =  new Events();
    $eventRecord = $event->fetch($_GET['eventID']);
    $event->eventID = $eventRecord['eventID'];
    $event->eventTitle= $eventRecord['eventTitle'];
    $event->eventStartDate= date('F d, Y', strtotime($eventRecord['eventStartDate'])); // Format the start date
    $event->eventEndDate= date('F d, Y', strtotime($eventRecord['eventEndDate'])); // Format the end date
    $event->eventStartTime= $eventRecord['eventStartTime'];
    $event->eventEndTime= $eventRecord['eventEndTime'];
    $participants = $event->getEventParticipant($_GET['eventID']);
    $participant = null;

    // Find the participant with the specified userID
    foreach ($participants as $p) {
        if ($p['userID'] == $_GET['userID']) {
            $participant = $p;
            break;
        }
    }

    $eventFacilitators = $event->getEventFacilitator($_GET['eventID']);
}

// Generate the certificate content
$certificate_content = "
    <div style='text-align: center; color: black;'>
        <h2>Certificate of Appreciation</h2>";

if ($participant) {
    $certificate_content .= "
        <p class='certificate-subtitle'>This certificate is awarded to</p>
        <h2 class='certificate-name'>" . $participant['fullName'] . "</h2>
        <p class='certificate-details'>in recognition for volunteering his time and work for the success of " . $event->eventTitle . " organized by Zamboanga City library, held from " . $event->eventStartDate . " to " . $event->eventEndDate . ".</p>";
} 

// Display facilitators' names
foreach($eventFacilitators as $facilitator){
    $certificate_content .= $facilitator['librarianFirstName'] . " " . $facilitator['librarianLastName'] . ", ";
}
$certificate_content = rtrim($certificate_content, ', ');

$certificate_content .= "<p class='certificate-footer'>Event Facilitator(s) ";
$certificate_content .= "</p>";

$certificate_content .= "</div>";

?>
<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Event Certificate';
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
                                <a href="../webpages/event-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo isset($_GET['eventID']) ? $_GET['eventID'] : ''; ?>" class="d-flex align-items-center">
                                    <i class='bx bx-arrow-back pe-3 back-icon'></i>
                                    <span class="back-text">Back</span>
                                </a>
                            </button>
                        </div>
                    </div>
                    <h2>Event Certificate</h2>
                    <!-- Certificate container -->
                    <div class="certificate-container" style="position: relative; width: 80%; margin: auto; height: 70vh; overflow: hidden;">
                        <!-- Background image -->
                        <img id="bgImg" src="../images/image.png" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" alt="Certificate Background">
                        <!-- Certificate content -->
                        <div id="certificateContent" class="certificate-content" style="position: absolute; top: 50%; left: 40%; transform: translate(-40%, -50%); text-align: center;">
                            <?php echo $certificate_content; ?>
                        </div>
                    </div>
                    <!-- Download button -->
                    <div style="text-align: center; margin-top: 20px;">
                        <button id="downloadCertificate" class="btn btn-primary">Download Certificate</button>
                        <button id="uploadCertificateBtn" class="btn btn-primary">Upload Certificate</button>
                    </div>
                    <!-- Hidden form for uploading certificate -->
                    <form id="uploadCertificateForm" action="upload_certificate.php" method="POST" enctype="multipart/form-data" style="display: none;">
                        <input type="hidden" name="eventID" value="<?php echo isset($_GET['eventID']) ? $_GET['eventID'] : ''; ?>">
                        <input type="hidden" name="userID" value="<?php echo isset($_GET['userID']) ? $_GET['userID'] : ''; ?>">
                        <input type="hidden" name="ecName" value="Certificate for Volunteers">
                        <input type="hidden" id="certificateImageData" name="certificateImageData">
                        <button type="submit" id="uploadCertificateSubmit" class="btn btn-primary">Upload Certificate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('../include/js2.php'); ?>
    <style>
        .certificate-subtitle {
            font-size: 14px;
        }
        .certificate-name {
            font-size: 24px;
        }
        .certificate-details {
            font-size: 12px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Function to handle certificate download
    function downloadCertificate() {
        var bgImg = new Image();
        bgImg.crossOrigin = 'anonymous';
        bgImg.onload = function() {
            var canvas = document.createElement('canvas');
            canvas.width = bgImg.width * 1.4; // Increase width by 20%
            canvas.height = bgImg.height * 1.4; // Increase height by 20%
            var ctx = canvas.getContext('2d');
            ctx.drawImage(bgImg, 0, 0, canvas.width, canvas.height); // Draw the image with adjusted size

            var certificateContentContainer = document.getElementById('certificateContent');
            var certificateContentImg = new Image();
            certificateContentImg.onload = function() {
                var x = (canvas.width - certificateContentImg.width) / 2;
                var y = (canvas.height - certificateContentImg.height) / 2;
                var marginTop = 50;
                var marginLeft = 0;
                var marginRight = 100;
                ctx.drawImage(certificateContentImg, x + marginLeft, y + marginTop);

                ctx.font = '20px "Poppins", sans-serif'; 
                ctx.fillStyle = '#FFFFFF'; 
                var imgURI = canvas.toDataURL('image/png');
                var dlLink = document.createElement('a');
                dlLink.download = 'certificate.png';
                dlLink.href = imgURI;
                dlLink.click();
            };
            certificateContentImg.src = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="' + canvas.width + '" height="' + canvas.height + '">' + '<foreignObject width="100%" height="100%">' + '<div xmlns="http://www.w3.org/1999/xhtml">' + certificateContentContainer.innerHTML + '</div>' + '</foreignObject>' + '</svg>');
        };
        bgImg.src = '../images/image.png';
    }

    function uploadCertificate() {
        var bgImg = new Image();
        bgImg.crossOrigin = 'anonymous';
        bgImg.onload = function() {
            var canvas = document.createElement('canvas');
            canvas.width = bgImg.width * 1.4;
            canvas.height = bgImg.height * 1.4;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(bgImg, 0, 0, canvas.width, canvas.height);

            var certificateContentContainer = document.getElementById('certificateContent');
            var certificateContentImg = new Image();
            certificateContentImg.onload = function() {
                var x = (canvas.width - certificateContentImg.width) / 2;
                var y = (canvas.height - certificateContentImg.height) / 2;
                var marginTop = 50;
                ctx.drawImage(certificateContentImg, x, y + marginTop);

                var imgURI = canvas.toDataURL('image/png');
                document.getElementById('certificateImageData').value = imgURI;

                var formData = new FormData(document.getElementById('uploadCertificateForm'));
                fetch('upload_certificate.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.text())
                  .then(result => {
                      Swal.fire({
                          title: "Success!",
                          text: "Certificate uploaded successfully.",
                          icon: "success"
                      }).then(() => {
                          window.location.href = "event-overview.php?librarianID=<?php echo $_SESSION['librarianID']; ?>&eventID=<?php echo isset($_GET['eventID']) ? $_GET['eventID'] : ''; ?>";
                      });
                  }).catch(error => {
                      Swal.fire({
                          title: "Error!",
                          text: "Failed to upload certificate.",
                          icon: "error"
                      }).then(() => {
                          window.history.back();
                      });
                  });
            };
            certificateContentImg.src = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="' + canvas.width + '" height="' + canvas.height + '">' + '<foreignObject width="100%" height="100%">' + '<div xmlns="http://www.w3.org/1999/xhtml">' + certificateContentContainer.innerHTML + '</div>' + '</foreignObject>' + '</svg>');
        };
        bgImg.src = '../images/image.png';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('downloadCertificate').addEventListener('click', downloadCertificate);
        document.getElementById('uploadCertificateBtn').addEventListener('click', function(event) {
            event.preventDefault();
            uploadCertificate();
        });
    });
    </script>
</body>
</html>
