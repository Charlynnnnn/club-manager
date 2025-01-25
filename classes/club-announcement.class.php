<?php

require_once 'database.php';

class Announcement {
    //attributes

    public $clubAnnouncementID;
    public $clubID;
    public $caTitle;
    public $caDescription;
    public $caCondition;
    public $caStartDate;
    public $caStartTime;
    public $caEndDate;
    public $caEndTime;

    protected $db;

    function __construct(){
        $this->db = new Database();
    }
    function add(){
        $sql = "INSERT INTO club_announcement (clubID, caTitle, caDescription, caCondition, caStartDate, caEndDate, caStartTime, caEndTime) VALUES 
        (:clubID, :caTitle, :caDescription, :caCondition, :caStartDate, :caEndDate, :caStartTime, :caEndTime);";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $this->clubID);
        $query->bindParam(':caTitle', $this->caTitle);
        $query->bindParam(':caDescription', $this->caDescription);
        $query->bindParam(':caCondition', $this->caCondition);
        $query->bindParam(':caStartDate', $this->caStartDate);
        $query->bindParam(':caEndDate', $this->caEndDate);
        $query->bindParam(':caStartTime', $this->caStartTime);
        $query->bindParam(':caEndTime', $this->caEndTime);
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }
    

    function edit(){
        $sql = "UPDATE club_announcement SET clubID = :clubID, caTitle = :caTitle, caDescription = :caDescription, caCondition = :caCondition, caStartDate = :caStartDate, caStartTime = :caStartTime, caEndDate = :caEndDate, caEndTime = :caEndTime  WHERE clubAnnouncementID  = :clubAnnouncementID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $this->clubID);
        $query->bindParam(':caTitle', $this->caTitle);
        $query->bindParam(':caDescription', $this->caDescription);
        $query->bindParam(':caCondition', $this->caCondition);
        $query->bindParam(':caStartDate', $this->caStartDate);
        $query->bindParam(':caStartTime', $this->caStartTime);
        $query->bindParam(':caEndDate', $this->caEndDate);
        $query->bindParam(':caEndTime', $this->caEndTime);
        $query->bindParam(':clubAnnouncementID', $this->clubAnnouncementID);
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function delete($clubAnnouncementID){
        $sql = "DELETE FROM club_announcement WHERE clubAnnouncementID = :clubAnnouncementID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubAnnouncementID', $clubAnnouncementID);
        return $query->execute();
    }

    function fetch($clubAnnouncementID){
        $sql = "SELECT * FROM club_announcement WHERE clubAnnouncementID = :clubAnnouncementID;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubAnnouncementID', $clubAnnouncementID);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function show(){
        $sql = "SELECT * FROM club_announcement ORDER BY caUpdatedAt DESC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
}

?>