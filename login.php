<?php
    session_start();
    if (isset($_SESSION['user']) && $_SESSION['user'] == 'librarian'){
        header('location: ./webpages/dashboard.php');
    }
    require_once('./classes/account.class.php');
    
    if (isset($_POST['login'])) {
        $account = new Account();
        $account->librarianEmail = htmlentities($_POST['librarianEmail']);
        $account->librarianPassword = htmlentities($_POST['librarianPassword']);
        if ($account->sign_in_librarian()){
            $_SESSION['user'] = 'librarian';
            $_SESSION['librarianID'] = $account->librarianID;
            header('location: ./webpages/dashboard.php?librarianID=' . $account->librarianID);

        }else{
            $error =  'Invalid email/password. Try again.';
        }
    }
    
    //if the above code is false then html below will be displayed
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../vendor/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custome css goes here -->
    <link rel="stylesheet" href="./css/style-lp.css">
    <link rel="icon" href="../images/zc_lib_seal.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet"/>
    <!-- Bootstrap DateTimePicker CSS and JavaScript -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Login</title>
</head>

<body>
    <div class="main">
        <!-- Login Card -->
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-12 col-md-6 col-lg-4 card-login p-4 px-5">
                <div class="row header d-flex justify-content-center mb-4">
                    <img src="./images/zc_lib_seal.png" class="logo-login" alt="">
                    <h4 class="header-title text-center">Zamboanga City Library <br> Club Manager</h4>
                </div>
               
                <form method="post" action="">
                    <div class="row d-flex justify-content-center">
                        <div class="input-group flex-column mb-3">
                            <label for="librarianEmail" class="label">Email</label>
                            <input type="email" name="librarianEmail" id="librarianEmail" class="input" placeholder="example@gmail.com"  required value="<?php if(isset($_POST['librarianEmail'])) { echo $_POST['librarianEmail']; } ?>">
                            <div></div>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center">
                        <div class="input-group flex-column mb-3">
                            <label for="librarianPassword" class="label">Password</label>
                            <input type="password" name="librarianPassword" id="librarianPassword" class="input" placeholder="Enter your password"  required value="<?php if(isset($_POST['librarianPassword'])) { echo $_POST['librarianPassword']; } ?>">
                            <div></div>
                        </div>
                    </div>

                    <button type="submit" class="sign-in-btn mt-4" name="login">SIGN IN</button>
                    <?php
                    if (isset($_POST['login']) && isset($error)){
                    ?>
                        <p class="text-danger mt-3 text-center"><?= $error ?></p>
                    <?php
                    }
                    ?>
                </form>
            </div>
        </div>
        <img src="./images/wave-bg-1.png" alt="Background Image" class="background-image"> <!-- Background Waves -->
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


</body>