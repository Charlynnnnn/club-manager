<?php
    
    function validate_field($field){
        $field = htmlentities($field);
        if(strlen(trim($field))<1){
            return false;
        }else{
            return true;
        }
    }

    function validate_email($librarianEmail){
        // Check if the 'email' key exists in the $_POST array
        if (isset($librarianEmail)) {
            $librarianEmail = trim($librarianEmail); // Trim whitespace
            // Check if the email is not empty
            if (empty($librarianEmail)) {
                return 'Email is required';
            } else if (!filter_var($librarianEmail, FILTER_VALIDATE_EMAIL)) {
                // Check if the email has a valid format
                return 'Email is invalid format';
            } else {
                return 'success';
            }
        } else {
            return 'Email is required'; // 'email' key doesn't exist in $_POST
        }
    }    

    function validate_password($librarianPassword) {
        $librarianPassword = htmlentities($librarianPassword);
        
        if (strlen(trim($librarianPassword)) < 1) {
            return "Password cannot be empty";
        } elseif (strlen($librarianPassword) < 8) {
            return "Password must be at least 8 characters long";
        } else {
            return "success"; // Indicates successful validation
        }
    }    

    function validate_cpw($librarianPassword, $cpassword){
        $pw = htmlentities($librarianPassword);
        $cpw = htmlentities($cpassword);
        if(strcmp($pw, $cpw) == 0){
            return true;
        }else{
            return false;
        }
    }

?>