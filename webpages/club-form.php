<?php
   
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: ./index.php');
    }
    require_once '../classes/clubs.class.php';
    if(isset($_GET['clubID'])){
        $club =  new Clubs();
        $record = $club->fetch($_GET['clubID']);
        $club->clubID = $record['clubID'];
        $club->clubName = $record['clubName'];
        $club->clubMinAge = $record['clubMinAge'];
        $club->clubMaxAge = $record['clubMaxAge'];
        $club->clubDescription = $record['clubDescription'];
        $clubManagers = $club->getClubManagers($_GET['clubID']);
        $memberCount = $club->getMemberCount($_GET['clubID']);
        $members = $club->getClubMembers($_GET['clubID']);
        $clubID = $_GET['clubID'];
    
        $existingQuestions = [];
        $existingQuestions = $club->fetchQuestions($clubID);
        
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update existing cfQuestions or insert new ones
        foreach ($_POST['cfQuestion'] as $index => $question) {
            $question = trim($question);
            if (!empty($question)) {
                if (isset($existingQuestions[$index]['clubFormQuestionID'])) {
                    // Update existing cfQuestion
                    $questionID = $existingQuestions[$index]['clubFormQuestionID'];
                    $club->updateClubFormQuestion($questionID, $question);
                } else {
                    // Insert new cfQuestion
                    $club->insertClubFormQuestion($clubID, $question);
                }
            }
        }
        // Redirect back to the same page to reload it
        header("Location: ../webpages/club-form.php?librarianID=" . $_SESSION['librarianID'] . "&clubID=$clubID");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Club Form';
  $activePage = 'clubs';
  require_once('../include/head.php');
?>

<body>


    <div class="main">
        <div class="row">
            <?php
                require_once('../include/nav-panel.php');
            ?>

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

                                <p class="pt-3">Club Form</p>
                            </div>

                            
                        </div>

                        <div class="row ps-2">
    <div class="row club-overview-details-container">
        <div class="col-12 col-lg-6" style="width:100%;">
            <form method="post" action="">
                <?php foreach ($existingQuestions as $question): ?>
                    <div class="position-relative mb-2">
                        <a href="delete_question.php?clubFormQuestionID=<?php echo $question['clubFormQuestionID']; ?>&clubID=<?php echo $clubID; ?>" class="btn btn-outline-danger border-2 position-absolute top-0 end-0"><span>X</span></a>
                        <textarea class="form-control" name="cfQuestion[]" required><?php echo htmlspecialchars($question['cfQuestion']); ?></textarea>
                        <div class="mb-3"></div>
                    </div>
                <?php endforeach; ?>
                <div id="extraFields"></div>
                <div class="mb-2 text-center">
                    <button type="button" id="addFieldsButton" class="btn btn-outline-danger border-2" style="background-color: white; border-color: black; border-style: dashed;"><i class="fa fa-plus text-danger"></i></button>
                </div>
                <button type="submit" name="save" class="btn btn-primary mt-4 mb-3" id="addClubFormButton" style="width: 80px;">Save</button>
            </form>
        </div>

        <script>
    document.getElementById("addFieldsButton").addEventListener("click", function() {
        var extraFields = document.getElementById("extraFields");
        var newFieldHTML = '<div class="mb-2"><textarea class="form-control" name="cfQuestion[]" rows="3" required></textarea></div>';
        extraFields.insertAdjacentHTML("beforeend", newFieldHTML);
    });
</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('../include/js2.php'); ?>

</body>