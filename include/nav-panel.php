<?php
require_once '../classes/librarian.class.php';
if(isset($_GET['librarianID'])){
    $librarian =  new Librarian();
    $record = $librarian->fetch($_GET['librarianID']);
    $librarian->librarianID = $record['librarianID'];
    $librarian->librarianID = $record['librarianID'];
    $librarian->librarianFirstName = $record['librarianFirstName'];
    $librarian->librarianMiddleName = $record['librarianMiddleName'];
    $librarian->librarianLastName = $record['librarianLastName'];
    $librarian->librarianDesignation = $record['librarianDesignation'];
    $librarian->librarianContactNo = $record['librarianContactNo'];
    $librarian->librarianEmail = $record['librarianEmail'];
    $librarian->librarianPassword = $record['librarianPassword'];
    $librarian->librarianImage = $record['librarianImage'];

}
?>

<head>
    <style>
        .navigation-list ul li.active {
            background-color: #6EC5E9;
        }
    </style>
</head>
<div class="side-panel col-11 col-md-4 col-lg-3 flex-column align-items-center">
    <div class="logo pt-4">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-3 d-flex ps-5"><img src="../images/zc_lib_seal.png" alt=""></div>
            <div class="col-9 logo-name ps-4">Zamboanga City Library </div>
        </div>
    </div>

    <div class="user d-flex flex-column justify-content-center mt-4">
        <div class="user-pic col-12 d-flex justify-content-center">
            <?php if(!empty($librarianImage)): ?>
                <img src="../images/<?php echo $librarianImage; ?>" alt="User Image">
            <?php else: ?>
                <img src="../images/user.png" alt="Default User Image">
            <?php endif; ?>
        </div>
        <div class="user-name pt-3 text-center">
            <a href="../webpages/librarian-profile.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">
                <?php echo $librarian->librarianFirstName," ", $librarian->librarianMiddleName," ",  $librarian->librarianLastName; ?>
            </a>
        </div>
        <div class="position text-center"><?php echo $librarian->librarianDesignation; ?></div>
    </div>
    
    <div class="navigation-list pt-5">
    <ul class="text-center">
        <li class="<?= ($activePage == 'dashboard') ? 'active' : '' ?>"><a href="../webpages/dashboard.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">Dashboard</a></li>
        <li class="<?= ($activePage == 'clubs') ? 'active' : '' ?>"><a href="../webpages/clubs.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">My Clubs</a></li>
        <li class="<?= ($activePage == 'events') ? 'active' : '' ?>"><a href="../webpages/events.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">My Events & Announcements</a></li>
        <li class="<?= ($activePage == 'users') ? 'active' : '' ?>"><a href="../webpages/users.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">Users</a></li>
        <li class="<?= ($activePage == 'attendance') ? 'active' : '' ?>"><a href="../webpages/attendance.php?librarianID=<?php echo $_SESSION['librarianID']; ?>">Attendance</a></li>
    </ul>
</div>

    

    <div class="d-flex justify-content-center">
        <div class="logout-btn text-center"><a href="../logout.php">Log out</a></div>
    </div>
</div>
