<?php

require_once 'database.php';

Class Events{
    //attributes

    public $eventID;
    public $eventTitle;
    public $eventDescription;
    public $eventGuestLimit;
    public $eventRegion;
    public $eventProvince;
    public $eventCity;
    public $eventBarangay;
    public $eventStreetName;
    public $eventBuildingName;
    public $eventZipCode;
    public $organizationClubID;
    public $eventStatus;
    public $eventCreatedAt;
    public $eventUpdatedAt;
    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function fetch($eventID){
        $sql = "SELECT * FROM event WHERE eventID = :eventID;";
        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        if($query->execute()){
            $data = $query->fetch();
        }
        return $data;
    }
    function getEventFacilitator($eventID){
        $sql = "SELECT librarian.* FROM event_facilitator 
                JOIN librarian ON event_facilitator.librarianID = librarian.librarianID 
                WHERE event_facilitator.eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    
    function getEventCollaboration($eventID){
        $sql = "SELECT organization_club.* FROM event_orgclub 
                JOIN organization_club ON event_orgclub.organizationClubID = organization_club.organizationClubID 
                WHERE event_orgclub.eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        $data = array(); 
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    
    public function fetchQuestions($eventID) {
        $sql = "SELECT erq.eventRegQuestionID, erq.erQuestion 
                FROM event_regquestion erq
                JOIN event_regform erf ON erq.eventRegistrationFormID = erf.eventRegistrationFormID
                WHERE erf.eventID = :eventID";
    
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID, PDO::PARAM_INT);
        
        if ($query->execute()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return []; // Return an empty array if there's an error
        }
    }
    public function updateQuestion($questionID, $newQuestion) {
        $sql = "UPDATE event_regquestion SET erQuestion = :newQuestion WHERE eventRegQuestionID = :questionID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':newQuestion', $newQuestion, PDO::PARAM_STR);
        $query->bindParam(':questionID', $questionID, PDO::PARAM_INT);
        
        return $query->execute();
    }
    public function addEventToForm($eventID) {
        // Check if the event is already added to event_regform
        $sql = "SELECT eventRegistrationFormID FROM event_regform WHERE eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            $sql = "INSERT INTO event_regform (eventID) VALUES (:eventID)";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':eventID', $eventID, PDO::PARAM_INT);
            $query->execute();
        }
    }
    
    public function getEventRegistrationFormID($eventID) {
        $sql = "SELECT eventRegistrationFormID FROM event_regform WHERE eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
    
        return $result['eventRegistrationFormID'];
    }
    
    public function addNewQuestion($eventRegistrationFormID, $newQuestion) {
        $sql = "INSERT INTO event_regquestion (eventRegistrationFormID, erQuestion) VALUES (:eventRegistrationFormID, :newQuestion)";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventRegistrationFormID', $eventRegistrationFormID, PDO::PARAM_INT);
        $query->bindParam(':newQuestion', $newQuestion, PDO::PARAM_STR);
        $query->execute();
    }
    
    
 
    function getEventRegistrant($eventID){
        $sql = "SELECT CONCAT(u.userFirstName, ' ', u.userMiddleName, ' ', u.userLastName) AS fullName,
                    u.userEmail,u.userContactNo,u.userGender,
                    CONCAT_WS(', ', 
                    NULLIF(u.userStreetName, ''), 
                    NULLIF(u.userBarangay, ''), 
                    NULLIF(u.userCity, ''), 
                    NULLIF(u.userProvince, ''), 
                    NULLIF(u.userZipCode, '')
                    ) AS address,
                        u.userAge,
                        er.erCreatedAt AS dateJoined
                    FROM event_registration er
                    JOIN user u ON er.userID = u.userID
                    WHERE er.eventID = :eventID"; // Corrected cm.cmstatus
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        $data = null;
        
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function getEventParticipant($eventID){
        $sql = "SELECT CONCAT(u.userFirstName, ' ', u.userMiddleName, ' ', u.userLastName) AS fullName,
                    u.userID,
                    u.userEmail,
                    u.userContactNo,
                    u.userGender,
                    CONCAT_WS(', ', 
                    NULLIF(u.userStreetName, ''), 
                    NULLIF(u.userBarangay, ''), 
                    NULLIF(u.userCity, ''), 
                    NULLIF(u.userProvince, ''), 
                    NULLIF(u.userZipCode, '')
                    ) AS address,
                    u.userAge,
                    ea.eaDate AS dateJoined
                FROM event_attendance ea
                JOIN event_attendanceuser eau ON ea.eventAttendanceID = eau.eventAttendanceID
                JOIN user u ON eau.userID = u.userID
                WHERE ea.eventID = :eventID";
                
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        $data = null;
        
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    
    function getEventVolunteers($eventID){
        $sql = "SELECT CONCAT(u.userFirstName, ' ', u.userMiddleName, ' ', u.userLastName) AS fullName,
                    u.userEmail,
                    u.userContactNo,
                    u.userGender,
                    CONCAT_WS(', ', 
                    NULLIF(u.userStreetName, ''), 
                    NULLIF(u.userBarangay, ''), 
                    NULLIF(u.userCity, ''), 
                    NULLIF(u.userProvince, ''), 
                    NULLIF(u.userZipCode, '')
                    ) AS address,
                    u.userAge,
                    er.dateRegistered AS dateJoined
                    FROM event_volunteer er
                    JOIN user u ON er.userID = u.userID
                    WHERE er.eventID = :eventID"; // Corrected cm.cmstatus
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        $data = null;
        
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function fetchImages($eventID) {
        $sql = "SELECT event_ImageID, eventImage FROM event_images WHERE eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        if ($query->execute()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
    
    function insertImage($eventID, $imageFilename) {
        $eventDate = date("Y-m-d"); // Get current date in MySQL format
        $sql = "INSERT INTO event_images (eventID, eventImage, eventDate) VALUES (:eventID, :imageFilename, :eventDate)";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        $query->bindParam(':imageFilename', $imageFilename); // Store filename instead of full path
        $query->bindParam(':eventDate', $eventDate);
        return $query->execute();
    }
    public function deleteImage($event_ImageID) {
        $sql = "DELETE FROM event_images WHERE event_ImageID = :event_ImageID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':event_ImageID', $event_ImageID);
        return $query->execute();
    }

    function getApplication() {
        $sql = "SELECT er.*, u.*, er.eventID 
        FROM event_registration er
        LEFT JOIN user u ON er.userID = u.userID
        ORDER BY CASE 
                    WHEN er.erStatus = 'Pending' THEN 1 
                    WHEN er.erStatus IN ('Approved', 'Rejected') THEN 2 
                END, 
                CASE 
                    WHEN er.erStatus IN ('Approved', 'Rejected') THEN er.erCreatedAt 
                END DESC";

        $query = $this->db->connect()->prepare($sql);
        
        $data = null;
    
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    
    
    function getUserDetails($userID) {
        $sql = "SELECT * FROM user WHERE userID = :userID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $userID);
        
        $data = null;
    
        if ($query->execute()) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function updateApplicationStatus($eventRegistrationID, $status) {
        $sql = "UPDATE event_registration SET erStatus = :status WHERE eventRegistrationID = :eventRegistrationID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':status', $status);
        $query->bindParam(':eventRegistrationID', $eventRegistrationID);
        
        return $query->execute();
    }
    function delete($eventRegQuestionID){
        $sql = "DELETE FROM  event_regquestion WHERE eventRegQuestionID = :eventRegQuestionID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventRegQuestionID', $eventRegQuestionID);
        return $query->execute();
    }
    public function add() {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("INSERT INTO event (eventTitle, eventDescription, eventStartDate, eventEndDate, eventStartTime, eventEndTime, eventGuestLimit, eventRegion, eventProvince, eventCity, eventBarangay, eventStreetName, eventBuildingName, eventZipCode) VALUES (:eventTitle, :eventDescription, :eventStartDate, :eventEndDate, :eventStartTime, :eventEndTime, :eventGuestLimit, :eventRegion, :eventProvince, :eventCity, :eventBarangay, :eventStreetName, :eventBuildingName, :eventZipCode)");
        $stmt->bindParam(':eventTitle', $this->eventTitle);
        $stmt->bindParam(':eventDescription', $this->eventDescription);
        $stmt->bindParam(':eventStartDate', $this->eventStartDate);
        $stmt->bindParam(':eventEndDate', $this->eventEndDate);
        $stmt->bindParam(':eventStartTime', $this->eventStartTime);
        $stmt->bindParam(':eventEndTime', $this->eventEndTime);
        $stmt->bindParam(':eventGuestLimit', $this->eventGuestLimit);
        $stmt->bindParam(':eventRegion', $this->eventRegion);
        $stmt->bindParam(':eventProvince', $this->eventProvince);
        $stmt->bindParam(':eventCity', $this->eventCity);
        $stmt->bindParam(':eventBarangay', $this->eventBarangay);
        $stmt->bindParam(':eventStreetName', $this->eventStreetName);
        $stmt->bindParam(':eventBuildingName', $this->eventBuildingName);
        $stmt->bindParam(':eventZipCode', $this->eventZipCode);
    
        if ($stmt->execute()) {
            $this->eventID = $conn->lastInsertId();
    
            // Add facilitators for the event
            foreach ($this->librarianIDs as $librarianID) {
                $stmt = $conn->prepare("INSERT INTO event_facilitator (eventID, librarianID) VALUES (:eventID, :librarianID)");
                $stmt->bindParam(':eventID',  $this->eventID);
                $stmt->bindParam(':librarianID', $librarianID);
                $stmt->execute();
            }
    
            // Add event collaborations
            foreach ($this->organizationClubIDs as $organizationClubID) {
                $stmt = $conn->prepare("INSERT INTO event_orgclub (eventID, organizationClubID) VALUES (:eventID, :organizationClubID)");
                $stmt->bindParam(':eventID',  $this->eventID);
                $stmt->bindParam(':organizationClubID', $organizationClubID);
                $stmt->execute();
            }
    
            // Add entry in club_event table
            if(isset($_GET['clubID'])) {
                $clubID = $_GET['clubID']; // Get clubID from URL parameter
                $status = 'Pending'; // Assuming status is always 'Pending' for newly added events
                $stmt = $conn->prepare("INSERT INTO club_event (clubID, eventID, status) VALUES (:clubID, :eventID, :status)");
                $stmt->bindParam(':clubID', $clubID);
                $stmt->bindParam(':eventID', $this->eventID);
                $stmt->bindParam(':status', $status);
                $stmt->execute();
            } else {
                // Handle case where clubID is not set
                return false;
            }
    
            return true;
        } else {
            return false;
        }
    }
    
    

    public function edit($eventID, $eventTitle, $eventDescription, $librarianIDs, $organizationClubIDs, $eventStartDate, $eventEndDate, $eventStartTime, $eventEndTime, $eventGuestLimit, $eventRegion, $eventProvince, $eventCity, $eventBarangay, $eventStreetName, $eventBuildingName, $eventZipCode, $eventStatus) {
        $conn = $this->db->connect();
        $sql = "UPDATE event SET eventTitle = :eventTitle, eventDescription = :eventDescription, eventStartDate = :eventStartDate, eventEndDate = :eventEndDate, eventStartTime = :eventStartTime, eventEndTime = :eventEndTime, eventGuestLimit = :eventGuestLimit, eventRegion = :eventRegion, eventProvince = :eventProvince, eventCity = :eventCity, eventBarangay = :eventBarangay, eventStreetName = :eventStreetName, eventBuildingName = :eventBuildingName, eventZipCode = :eventZipCode, eventStatus = :eventStatus WHERE eventID = :eventID";
        $query = $conn->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        $query->bindParam(':eventTitle', $eventTitle);
        $query->bindParam(':eventDescription', $eventDescription);
        $query->bindParam(':eventStartDate', $eventStartDate);
        $query->bindParam(':eventEndDate', $eventEndDate);
        $query->bindParam(':eventStartTime', $eventStartTime);
        $query->bindParam(':eventEndTime', $eventEndTime);
        $query->bindParam(':eventGuestLimit', $eventGuestLimit);
        $query->bindParam(':eventRegion', $eventRegion);
        $query->bindParam(':eventProvince', $eventProvince);
        $query->bindParam(':eventCity', $eventCity);
        $query->bindParam(':eventBarangay', $eventBarangay);
        $query->bindParam(':eventStreetName', $eventStreetName);
        $query->bindParam(':eventBuildingName', $eventBuildingName);
        $query->bindParam(':eventZipCode', $eventZipCode);
        $query->bindParam(':eventStatus', $eventStatus);
    
        if ($query->execute()) {
            // Update event facilitators
            $stmt = $conn->prepare("DELETE FROM event_facilitator WHERE eventID = :eventID");
            $stmt->bindParam(':eventID', $eventID);
            $stmt->execute();
    
            foreach ($librarianIDs as $librarianID) {
                $stmt = $conn->prepare("INSERT INTO event_facilitator (eventID, librarianID) VALUES (:eventID, :librarianID)");
                $stmt->bindParam(':eventID', $eventID);
                $stmt->bindParam(':librarianID', $librarianID);
                $stmt->execute();
            }
    
            // Update event collaborations
            $stmt = $conn->prepare("DELETE FROM event_orgclub WHERE eventID = :eventID");
            $stmt->bindParam(':eventID', $eventID);
            $stmt->execute();
    
            foreach ($organizationClubIDs as $organizationClubID) {
                $stmt = $conn->prepare("INSERT INTO event_orgclub (eventID, organizationClubID) VALUES (:eventID, :organizationClubID)");
                $stmt->bindParam(':eventID', $eventID);
                $stmt->bindParam(':organizationClubID', $organizationClubID);
                $stmt->execute();
            }
    
            return true;
        } else {
            return false;
        }
    }

    public function fetchLibrarian($librarianID) {
        $sql = "SELECT * FROM librarian WHERE librarianID = :librarianID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianID', $librarianID);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
// events.class.php
public function fetchClubEvent($clubID) {
    $sql = "SELECT e.*
            FROM club_event ce
            JOIN event e ON ce.eventID = e.eventID
            WHERE ce.clubID = :clubID";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':clubID', $clubID);
    if ($query->execute()) {
        $data = $query->fetchAll(PDO::FETCH_OBJ); // Fetch events as objects
    }
    return $data;
}
public function show($librarianID) {
    $sql = "SELECT * FROM event
            LEFT JOIN event_facilitator ef ON event.eventID = ef.eventID
            JOIN librarian l ON ef.librarianID = l.librarianID
            WHERE ef.librarianID = :librarianID
            ORDER BY eventCreatedAt ASC";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':librarianID', $librarianID);
    $data = null;

    if ($query->execute()) {
        $data = $query->fetchAll();

        foreach ($data as &$item) {
            $currentDateTime = date('Y-m-d H:i:s'); // Get current date and time in MySQL format
            $eventStartTime = date('Y-m-d H:i:s', strtotime($item['eventStartDate'] . ' ' . $item['eventStartTime']));
            $eventEndTime = date('Y-m-d H:i:s', strtotime($item['eventEndDate'] . ' ' . $item['eventEndTime']));

            // Determine event status
            if ($currentDateTime < $eventStartTime) {
                $item['eventStatus'] = 'Upcoming';
            } elseif ($currentDateTime >= $eventStartTime && $currentDateTime <= $eventEndTime) {
                $item['eventStatus'] = 'Ongoing';
            } else {
                $item['eventStatus'] = 'Completed';
            }
        }
    }
    return $data ?: [];
}

    public function getAllEvents() {
        $sql = "SELECT * FROM event";
        $query = $this->db->connect()->prepare($sql);

        $events = null;

        if ($query->execute()) {
            $events = $query->fetchAll();
        }

        return $events;
    }

    public function delete_event($eventID) {
        $sql = "DELETE FROM event WHERE eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);

        return $query->execute();
    }

    public function checkEventConflict() {
        $startDateTime = date('Y-m-d H:i:s', strtotime($this->eventStartDate . ' ' . $this->eventStartTime));
        $endDateTime = date('Y-m-d H:i:s', strtotime($this->eventEndDate . ' ' . $this->eventEndTime));
    
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM event WHERE (eventStartDate < :endDateTime AND eventEndDate > :startDateTime)
        OR (eventStartDate = :startDateTime AND eventEndDate >= :endDateTime)
        OR (eventStartDate <= :startDateTime AND eventEndDate = :endDateTime)");
        $stmt->bindParam(':startDateTime', $startDateTime);
        $stmt->bindParam(':endDateTime', $endDateTime);
        $stmt->execute();
    
        return $stmt->rowCount() > 0;
    }

    function getApprovedOrganizationClubs() {
        $sql = "SELECT * FROM organization_club WHERE ocStatus = 'Approved'";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
    
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
    
        return $data ?: [];
    }
    
    public function getEventCollaborations($eventID) {
        $sql = "SELECT * FROM organization_club WHERE organizationClubID IN (SELECT organizationClubID FROM event_orgclub WHERE eventID = :eventID)";
        $conn = $this->db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':eventID', $eventID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventCollaborationDetails($eventID) {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT oc.ocName AS organizationClubName 
                               FROM event_orgclub AS eoc 
                               JOIN organization_club AS oc ON eoc.organizationClubID = oc.organizationClubID 
                               WHERE eoc.eventID = :eventID");
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEventStatus($eventStartDate, $eventEndDate, $eventStartTime, $eventEndTime) {
        $currentDateTime = date('Y-m-d H:i:s');
        $startDateTime = date('Y-m-d H:i:s', strtotime($eventStartDate . ' ' . $eventStartTime));
        $endDateTime = date('Y-m-d H:i:s', strtotime($eventEndDate . ' ' . $eventEndTime));
    
        if ($currentDateTime < $startDateTime) {
            return 'Upcoming';
        } elseif ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
            return 'Ongoing';
        } else {
            return 'Finished';
        }
    }
    
    
}
?>
