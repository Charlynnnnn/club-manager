<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
    header('location: ./index.php');
}

if(isset($_GET['eventRegistrationID'])) {
    $_SESSION['eventRegistrationID'] = $_GET['eventRegistrationID'];
}
if(isset($_GET['eventRegistrationFormID'])) {
    $_SESSION['eventRegistrationFormID'] = $_GET['eventRegistrationFormID'];
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
$title = 'Overview';
$activePage = 'evente';
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
                        </div>
                    </div>
                    <div class="row ps-2">
                        <div class="row event-overview-details-container">
                            <h2>Application Overview</h2>
                            <div class="application-container border p-3">
                                <?php 
                                require_once '../classes/eventoverview.class.php';
                                $eventoverview= new EventOverview();
                                $eventRegistrationID = $_SESSION['eventRegistrationID'] ?? null; 
                                $eventRegistrationFormID = $_SESSION['eventRegistrationFormID'] ?? null; 
                                if($eventRegistrationID) {
                                    $userInfo = $eventoverview->getUserInfo($eventRegistrationID);
                                    $formQuestions = $eventoverview->getFormQuestions($eventRegistrationFormID);
                                    $fullName = $userInfo['userFirstName'] . ' ' . $userInfo['userMiddleName'] . ' ' . $userInfo['userLastName'];
                                    $eventID = $userInfo['eventID'];
                                    $eventInfo = $eventoverview->getEventInfo($eventID);
                                    $eventTitle = $eventInfo['eventTitle'];
                                    $formQuestions = $eventoverview->getFormQuestions($eventRegistrationFormID);
                                    $formAnswers = $eventoverview->getFormAnswers($eventRegistrationID);
                                }else{
                                    echo "no information fetched.";
                                }
                                ?>
                                <?php if ($userInfo && isset($eventTitle) && isset($formQuestions) && isset($formAnswers)): ?>
                                <div class="applicant-info">
                                    <?php if ($userInfo): ?>
                                        <div class="row mb-3">
                                            <div class="col-4 fw-bold">Applicant Name:</div>
                                            <div class="col-8"><?php echo $fullName; ?> (<?php echo $userInfo['userUserName']; ?>) </div>
                                        </div>
                                        <div class="row">
            <div class="col-4 fw-bold">Email Address:</div>
            <div class="col-8"><?php echo $userInfo['userEmail']; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">Birthdate:</div>
            <div class="col-8"><?php echo isset($userInfo['userBirthdate']) ? date("F j, Y", strtotime($userInfo['userBirthdate'])) : ''; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">Age:</div>
            <div class="col-8"><?php echo $userInfo['userAge']; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">Gender:</div>
            <div class="col-8"><?php echo $userInfo['userGender']; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">Civil Status:</div>
            <div class="col-8"><?php echo $userInfo['userCivilStatus']; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">Contact Number:</div>
            <div class="col-8"><?php echo $userInfo['userContactNo']; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">School/Office:</div>
            <div class="col-8"><?php echo $userInfo['userSchoolOffice']; ?></div>
        </div>
        <div class="row">
            <div class="col-4 fw-bold">Address:</div>
            <div class="col-8"><?php echo isset($userInfo['userStreetName']) ? $userInfo['userStreetName'] . ',' : ''; ?> <?php echo isset($userInfo['userBarangay']) ? $userInfo['userBarangay'] . ',' : ''; ?> <?php echo isset($userInfo['userCity']) ? $userInfo['userCity'] . ',' : ''; ?> <?php echo isset($userInfo['userProvince']) ? $userInfo['userProvince'] . ',' : ''; ?> <?php echo isset($userInfo['userZipCode']) ? $userInfo['userZipCode'] : ''; ?></div>
        </div>
        <div class="row mt-3">
            <div class="col-4 fw-bold">Event Title:</div>
            <div class="col-8"><?php echo isset($eventTitle) ? $eventTitle : ''; ?></div>
        </div>
                                        <div class="row">
                                            <div class="col-4 fw-bold">Form Questions:</div>
                                            <div class="col-8">
                                                <ul>
                                                    <?php foreach ($formQuestions ?? [] as $question): ?>
                                                        <li><?php echo $question; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4 fw-bold">Form Answers:</div>
                                            <div class="col-8">
                                                <ul>
                                                    <?php foreach ($formAnswers ?? [] as $answer): ?>
                                                        <li><?php echo $answer; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p>No applicant information found.</p>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                    <p>No applicant information found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('../include/js2.php'); ?>
</body>
</html>
