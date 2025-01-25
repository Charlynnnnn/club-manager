<?php

require_once 'database.php';

Class ClubOverview {
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function fetch($record_id) {
        $sql = "SELECT * FROM user WHERE userID = :userID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $record_id);
        if($query->execute()) {
            $data = $query->fetch();
            return $data;
        } else {
            return false;
        }
    }

    function getUserInfo($clubMembershipID) {
        $sql = "SELECT u.*, cm.clubID FROM user u JOIN club_membership cm ON u.userID = cm.userID WHERE cm.clubMembershipID = :clubMembershipID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubMembershipID', $clubMembershipID);
        if ($query->execute()) {
            $userInfo = $query->fetch(PDO::FETCH_ASSOC);
            return $userInfo;
        } else {
            return false;
        }
    }

    function getClubInfo($clubID) {
        $sql = "SELECT clubName FROM club WHERE clubID = :clubID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);
        if ($query->execute()) {
            $clubInfo = $query->fetch(PDO::FETCH_ASSOC);
            return $clubInfo;
        } else {
            return false;
        }
    }

    function getFormQuestions($clubID) {
        $sql = "SELECT cfQuestion FROM club_formquestion WHERE clubID = :clubID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);
        if ($query->execute()) {
            $formQuestions = $query->fetchAll(PDO::FETCH_COLUMN);
            return $formQuestions;
        } else {
            return false;
        }
    }

    function getFormAnswers($clubMembershipID) {
        $sql = "SELECT cfAnswer FROM club_formanswer WHERE clubMembershipID = :clubMembershipID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubMembershipID', $clubMembershipID);
        if ($query->execute()) {
            $formAnswers = $query->fetchAll(PDO::FETCH_COLUMN);
            return $formAnswers;
        } else {
            return false;
        }
    }
}
?>
