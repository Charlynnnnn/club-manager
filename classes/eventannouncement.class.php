<?php

require_once 'database.php';

Class EventsAnnouncement{
    public $clubAnnouncementID;
    public $clubID;
    public $caTitle;
    public $caDescription;
    public $caCondition;
    public $caStartDate;
    public $caStartTime;
    public $caEndDate;
    public $caEndTime;
    public $caCreatedAt;
    public $caUpdatedAt;
    protected $db;

    function __construct(){
        $this->db = new Database();
    }

   // PHP Script (clubannouncement.class.php)
function add($clubID){
    $sql = "INSERT INTO club_announcement (clubID, caTitle, caDescription, caDate, caTime, caCondition) VALUES 
    (:clubID, :caTitle, :caDescription, :caStartDate, :caStartTime, :caCondition);";

    $query=$this->db->connect()->prepare($sql);
    $query->bindParam(':clubID', $clubID);
    $query->bindParam(':caTitle', $this->caTitle);
    $query->bindParam(':caDescription', $this->caDescription);
    $query->bindParam(':caStartDate', $this->caStartDate);
    $query->bindParam(':caStartTime', $this->caStartTime);
    $query->bindParam(':caCondition', $this->caCondition);

    if($query->execute()){
        return true;
    } else {
        return false;
    }   
}
function edit($clubID,$clubAnnouncementID){
    $sql = "UPDATE club_announcement SET clubID=:clubID, caTitle=:caTitle, caDescription=:caDescription, caCondition=:caCondition, caDate=:caStartDate, caTime=:caStartTime WHERE clubAnnouncementID = :clubAnnouncementID;";
    
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':clubID', $this->clubID);
    $query->bindParam(':caTitle', $this->caTitle);
    $query->bindParam(':caDescription', $this->caDescription);
    $query->bindParam(':caCondition', $this->caCondition);
    $query->bindParam(':caStartDate', $this->caStartDate);
    $query->bindParam(':caStartTime', $this->caStartTime);
    $query->bindParam(':clubAnnouncementID', $this->clubAnnouncementID);
    
    try {
        if($query->execute()){
            return true;
        } else {
            return false;
        }
    } catch(PDOException $e) {
        // Log error message
        error_log("Error updating club announcement: " . $e->getMessage());
        return false;
    }
}



    
    public function fetch($clubAnnouncementID) {
        $sql = "SELECT * FROM club_announcement WHERE clubAnnouncementID = :clubAnnouncementID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubAnnouncementID', $clubAnnouncementID);
        $query->execute();
        
        // Fetch data as an associative array
        $announcement_data = $query->fetch(PDO::FETCH_ASSOC);
    
        // Check if data is found
        if ($announcement_data) {
            return $announcement_data; // Return the fetched data
        } else {
            return false; // Return false if data is not found
        }
    }
    function show(){
        $sql = "SELECT * FROM event_announcement ORDER BY eaTitle ASC;";
        $query=$this->db->connect()->prepare($sql);
        $data = null;
        if($query->execute()){
            $data = $query->fetchAll();
        }
        return $data;
    }
    function delete($clubAnnouncementID){
        $sql = "DELETE FROM club_announcement WHERE clubAnnouncementID = :clubAnnouncementID;";
        
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubAnnouncementID', $clubAnnouncementID);
        
        if($query->execute()){
            return true;
        } else {
            return false;
        }
    }
    
}

?>