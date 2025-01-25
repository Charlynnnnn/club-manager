<?php

require_once 'database.php';

class Account{

    public $librarianID;
    public $librarianEmail;
    public $librarianPassword;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function sign_in_librarian(){
        $sql = "SELECT * FROM librarian WHERE librarianEmail = :librarianEmail and librarianEmployment ='Active' LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianEmail', $this->librarianEmail);
    
        if ($query->execute()) {
            $accountData = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($accountData && password_verify($this->librarianPassword, $accountData['librarianPassword'])) {
                $this->librarianID = $accountData['librarianID'];
                return true;
            }
        }
    
        return false;
    }

   
}

?>