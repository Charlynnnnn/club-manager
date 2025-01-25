<?php

require_once 'database.php';

Class Librarian{
    //attributes

    public $librarianID;
    public $librarianFirstName;
    public $librarianMiddleName;
    public $librarianLastName;
    public $librarianDesignation;
    public $librarianContactNo;
    public $librarianEmail;
    public $librarianPassword;
    public $librarianImage;

    protected $db;

    function __construct(){
        $this->db = new Database();
    }
  

    function fetch($record_id){
        $sql = "SELECT * FROM librarian WHERE librarianID = :librarianID;";
        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':librarianID', $record_id);
        if($query->execute()){
            $data = $query->fetch();
        }
        return $data;
    }

    function show(){
        $sql = "SELECT * FROM librarian";
        $query=$this->db->connect()->prepare($sql);
        $data = null;
        if($query->execute()){
            $data = $query->fetchAll();
        }
        return $data;
    }
    function TotalUser() {
        $sql = "SELECT COUNT(*) AS user_count FROM user";
        $query = $this->db->connect()->prepare($sql);
        $memberCount = 0;
        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['user_count'])) {
                $memberCount = $result['user_count'];
            }
        }
        return $memberCount;
    }
    function TotalClub() {
        $sql = "SELECT COUNT(*) AS club_count FROM club";
        $query = $this->db->connect()->prepare($sql);
        $club_count = 0;
        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['club_count'])) {
                $club_count = $result['club_count'];
            }
        }
        return $club_count;
    }

    function UpcomingEvents() {
        $sql = "SELECT COUNT(*) AS upcoming_events_count FROM event WHERE eventStartDate > NOW()";
        $query = $this->db->connect()->prepare($sql);
        $upcoming_events_count = 0;
        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['upcoming_events_count'])) {
                $upcoming_events_count = $result['upcoming_events_count'];
            }
        }
        return $upcoming_events_count;
    }
    function PendingProposal() {
        $sql = "SELECT COUNT(*) AS pendingproposal_count FROM org_proposal WHERE status = 'Pending'";
        $query = $this->db->connect()->prepare($sql);
        $pendingproposal_count = 0;
        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['pendingproposal_count'])) {
                $pendingproposal_count = $result['pendingproposal_count'];
            }
        }
        return $pendingproposal_count;
    }
    function edit() {
        $sql = "UPDATE librarian SET 
                librarianEmail = :librarianEmail, 
                librarianContactNo = :librarianContactNo, 
                librarianDesignation = :librarianDesignation, 
                librarianPassword = :librarianPassword,
                librarianFirstName = :librarianFirstName, 
                librarianLastName = :librarianLastName, 
                librarianMiddleName = :librarianMiddleName, 
                librarianImage = :librarianImage,
                librarianEmployment = 'Active'
                WHERE librarianID = :librarianID";
        
        try {
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':librarianID', $this->librarianID);
            $query->bindParam(':librarianEmail', $this->librarianEmail);
            $query->bindParam(':librarianContactNo', $this->librarianContactNo);
            $query->bindParam(':librarianDesignation', $this->librarianDesignation);
            $query->bindParam(':librarianPassword', $this->librarianPassword);
            $query->bindParam(':librarianImage', $this->librarianImage);
            $query->bindParam(':librarianFirstName', $this->librarianFirstName);
            $query->bindParam(':librarianMiddleName', $this->librarianMiddleName);
            $query->bindParam(':librarianLastName', $this->librarianLastName);
        
            if ($query->execute()) {
                return true;
            } else {
                $errorInfo = $query->errorInfo();
                error_log("Database Error: " . $errorInfo[2]);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Database Exception: " . $e->getMessage());
            return false;
        }
    }
    
    
    function is_email_exist(){
        $sql = "SELECT * FROM librarian WHERE librarianEmail = :librarianEmail;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianEmail', $this->librarianEmail);
        if($query->execute()){
            if($query->rowCount() > 0){
                return true;
            }
        }
        return false;
    }
    function validateInputs($librarian, $old_email) {
        $valid = true;
    
        if (!validate_field($librarian->librarianFirstName) ||
            !validate_field($librarian->librarianLastName) ||
            !validate_field($librarian->librarianDesignation) ||
            !validate_field($librarian->librarianContactNo) ||
            !validate_field($librarian->librarianEmail) ||
            !validate_field($librarian->librarianPassword) ||
            !validate_email($librarian->librarianEmail)) {
            $valid = false;
        }
    
        if ($librarian->is_email_exist() && $librarian->librarianEmail !== $old_email) {
            $valid = false;
            $email_error = "Email already exists";
        }
    
        return $valid;
    }
    function getAvailablelibrarian() {
        // You need to implement logic to get available librarian based on date and time
        // Example: SELECT * FROM librarian WHERE availability = 'Available'
        $sql = "SELECT * FROM librarian WHERE librarianEmployment = 'Active'";
        $query = $this->db->connect()->prepare($sql);

        $librarians = null;

        if ($query->execute()) {
            $librarians = $query->fetchAll();
        }

        return $librarians;
    }
}

?>