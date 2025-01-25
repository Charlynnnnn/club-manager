<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
    header('location: ./index.php');
}

// Assuming clubMembershipID is passed via query string
if(isset($_GET['clubMembershipID'])) {
    $_SESSION['clubMembershipID'] = $_GET['clubMembershipID'];
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
  $title = 'Clubs';
  $activePage = 'clubs';
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
                                <a href="./clubs.php?librarianID=<?php echo $_SESSION['librarianID']; ?>" class="d-flex align-items-center">
                                    <i class='bx bx-arrow-back pe-3 back-icon'></i>
                                    <span class="back-text">Back</span>
                                </a>
                            </button>
                        </div>

                        
                    </div>

                    <div class="row ps-2">
                        <div class="row club-overview-details-container">
                            <h2>Application Overview</h2>
                            <div class="application-container border p-3">
                                <?php 
                                require_once '../classes/cluboverview.class.php';
                                $cluboverview= new ClubOverview();
                                
                                // Get clubMembershipID from session
                                $clubMembershipID = $_SESSION['clubMembershipID'] ?? null; 
                                
                                // Fetch user information based on clubMembershipID
                                if($clubMembershipID) {
                                    $userInfo = $cluboverview->getUserInfo($clubMembershipID);
                                    $fullName = $userInfo['userFirstName'] . ' ' . $userInfo['userMiddleName'] . ' ' . $userInfo['userLastName'];
                                    $clubID = $userInfo['clubID'];
                                    $clubInfo = $cluboverview->getClubInfo($clubID);
                                    $clubName = $clubInfo['clubName'];
                                    $formQuestions = $cluboverview->getFormQuestions($clubID);
                                    $formAnswers = $cluboverview->getFormAnswers($clubMembershipID);
                                }
                                ?>
                            <div class="applicant-info">
    <div class="row mb-3">
        <div class="col-4 fw-bold">Applicant Name:</div>
        <div class="col-8"><?php echo $fullName ?? '<span class="text-danger">No name found</span>'; ?> (<?php echo isset($userInfo['userUserName']) ? $userInfo['userUserName'] : '<span class="text-danger">No username found</span>'; ?>) </div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">Email Address:</div>
        <div class="col-8"><?php echo isset($userInfo['userEmail']) ? $userInfo['userEmail'] : '<span class="text-danger">No email found</span>'; ?></div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">Birthdate:</div>
        <div class="col-8"><?php echo ($userInfo['userBirthdate'] !== '0000-00-00' && isset($userInfo['userBirthdate'])) ? date("F j, Y", strtotime($userInfo['userBirthdate'])) : '<span class="text-danger">No birthdate found</span>'; ?></div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">Age:</div>
        <div class="col-8">
    <?php 
        if(isset($userInfo['userAge'])) {
            echo $userInfo['userAge'] != 0 ? $userInfo['userAge'] : '<span class="text-danger">No age found</span>';
        } else {
            echo '<span class="text-danger">No age found</span>';
        }
    ?>
</div>

    </div>
    <div class="row">
        <div class="col-4 fw-bold">Gender:</div>
        <div class="col-8"><?php echo isset($userInfo['userGender']) != ' ' ? $userInfo['userGender'] : '<span class="text-danger">No gender found</span>'; ?></div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">Civil Status:</div>
        <div class="col-8"><?php echo isset($userInfo['userCivilStatus']) != ' ' ? $userInfo['userCivilStatus'] : '<span class="text-danger">No civil status found</span>'; ?></div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">Contact Number:</div>
        <div class="col-8"><?php echo isset($userInfo['userContactNo']) != ' ' ? $userInfo['userContactNo'] : '<span class="text-danger">No contact number found</span>'; ?></div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">School/Office:</div>
        <div class="col-8"><?php echo isset($userInfo['userSchoolOffice']) != ' ' ? $userInfo['userSchoolOffice'] : '<span class="text-danger">No school/office found</span>'; ?></div>
    </div>
    <div class="row">
        <div class="col-4 fw-bold">Address:</div>
        <div class="col-8">
    <?php 
    $addressParts = [];
    if (!empty($userInfo['userStreetName'])) {
        $addressParts[] = $userInfo['userStreetName'];
    }
    if (!empty($userInfo['userBarangay'])) {
        $addressParts[] = $userInfo['userBarangay'];
    }
    if (!empty($userInfo['userCity'])) {
        $addressParts[] = $userInfo['userCity'];
    }
    if (!empty($userInfo['userProvince'])) {
        $addressParts[] = $userInfo['userProvince'];
    }
    if (!empty($userInfo['userZipCode'])) {
        $addressParts[] = $userInfo['userZipCode'];
    }

    if (!empty($addressParts)) {
        echo implode(', ', $addressParts);
    } else {
        echo '<span class="text-danger">No address found</span>';
    }
    ?>
</div>

    </div>
    <div class="row mt-3">
        <div class="col-4 fw-bold">Club Name:</div>
        <div class="col-8"><?php echo $clubName ?? '<span class="text-danger">No club name found</span>'; ?></div>
    </div>
    <div class="row">
    <div class="col-4 fw-bold">Form Questions:</div>
    <div class="col-8">
        <?php if (!empty($formQuestions)): ?>
            <ul>
                <?php foreach ($formQuestions as $question): ?>
                    <li><?php echo $question; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No form questions found.</p>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-4 fw-bold">Form Answers:</div>
    <div class="col-8">
        <?php if (!empty($formAnswers)): ?>
            <ul>
                <?php foreach ($formAnswers as $answer): ?>
                    <li><?php echo $answer; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No form answers found.</p>
        <?php endif; ?>
    </div>
</div>

</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('../include/js2.php'); ?>
</body>
