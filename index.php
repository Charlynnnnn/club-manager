<?php

    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian'){
        header('location: login.php');
    }