<?php
require_once 'database.php';

class EventOverview {
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function getUserInfo($eventRegistrationID) {
        $sql = "SELECT u.*, er.eventID FROM user u JOIN event_registration er ON u.userID = er.userID WHERE er.eventRegistrationID = :eventRegistrationID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventRegistrationID', $eventRegistrationID);
        if (!$query->execute()) {
            die("Error in executing SQL query: " . $query->errorInfo()[2]);
        }
        $userInfo = $query->fetch(PDO::FETCH_ASSOC);
        return $userInfo;
    }

    function getEventInfo($eventID) {
        $sql = "SELECT eventTitle FROM event WHERE eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        if (!$query->execute()) {
            die("Error in executing SQL query: " . $query->errorInfo()[2]);
        }
        $eventInfo = $query->fetch(PDO::FETCH_ASSOC);
        return $eventInfo;
    }

    function getFormQuestions($eventRegistrationFormID) {
        $sql = "SELECT erQuestion FROM event_regquestion WHERE eventRegistrationFormID = :eventRegistrationFormID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventRegistrationFormID', $eventRegistrationFormID);
        if (!$query->execute()) {
            die("Error in executing SQL query: " . $query->errorInfo()[2]);
        }
        $formQuestions = $query->fetchAll(PDO::FETCH_COLUMN);
        return $formQuestions;
    }

    function getFormAnswers($eventRegistrationID) {
        $sql = "SELECT erAnswer FROM event_reganswer WHERE eventRegistrationID = :eventRegistrationID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventRegistrationID', $eventRegistrationID);
        if (!$query->execute()) {
            die("Error in executing SQL query: " . $query->errorInfo()[2]);
        }
        $formAnswers = $query->fetchAll(PDO::FETCH_COLUMN);
        return $formAnswers;
    }
}

?>
