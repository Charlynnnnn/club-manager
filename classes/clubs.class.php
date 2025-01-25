<?php

require_once 'database.php';

Class Clubs{
    //attributes

    public $clubID;
    public $userID;
    public $clubMembershipID;
    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function fetch($record_id){
        $sql = "SELECT * FROM club WHERE clubID = :clubID";
        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $record_id);
        if($query->execute()){
            $data = $query->fetch();
        }
        return $data;
    }

    function show($librarianID){
        $sql = "SELECT c.*, 
                CONCAT(c.clubMinAge, '-', c.clubMaxAge) AS age_range,
                COUNT(DISTINCT CASE WHEN cm1.cmstatus = 'Approved' THEN cm1.clubmembershipID ELSE NULL END) AS members
                FROM club c
                LEFT JOIN club_management cm ON c.clubID = cm.clubID
                JOIN librarian l ON cm.librarianID = l.librarianID
                LEFT JOIN club_membership cm1 ON c.clubID = cm1.clubID
                WHERE cm.librarianID = :librarianID
                GROUP BY c.clubID, age_range
                ORDER BY c.clubID ASC";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianID', $librarianID);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function getClubManagers($clubID)
    {
        $sql = "SELECT librarian.* FROM club_management 
                JOIN librarian ON club_management.librarianID = librarian.librarianID 
                WHERE club_management.clubID = :clubID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function getMemberCount($clubID){
        $sql = "SELECT COUNT(*) AS member_count FROM club_membership WHERE clubID = :clubID AND cmstatus = 'Approved'";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);
        $memberCount = 0;
        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $memberCount = $result['member_count'];
        }
        return $memberCount;
    }   
    function getClubMembers($clubID){
        $sql = "SELECT CONCAT(u.userFirstName, ' ', u.userMiddleName, ' ', u.userLastName) AS fullName,
                    u.userEmail,
                    u.userContactNo,
                    u.userGender,
                    CONCAT_WS(
                        ', ',
                        NULLIF(u.userStreetName, ''),
                        NULLIF(u.userBarangay, ''),
                        NULLIF(u.userCity, ''),
                        NULLIF(u.userProvince, ''),
                        NULLIF(u.userZipCode, '')
                    ) AS address,                    
                    cm.cmCreatedAt AS dateJoined
                FROM club_membership cm
                JOIN user u ON cm.userID = u.userID
                WHERE cm.clubID = :clubID AND cm.cmstatus = 'Approved'";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);
        $data = null;
        
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function getApplication($librarianID) {
        $sql = "SELECT cm.*, u.*, cm.clubID 
                FROM club_membership cm
                INNER JOIN user u ON cm.userID = u.userID
                INNER JOIN club_management cman ON cm.clubID = cman.clubID
                WHERE cman.librarianID = :librarianID
                ORDER BY CASE 
                            WHEN cm.cmStatus = 'Pending' THEN 1 
                            WHEN cm.cmStatus IN ('Approved', 'Rejected') THEN 2 
                        END, 
                        CASE 
                            WHEN cm.cmstatus IN ('Approved', 'Rejected') THEN cm.cmCreatedAt 
                        END DESC";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianID', $librarianID);
        
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
    function updateApplicationStatus($clubMembershipID, $status) {
        $sql = "UPDATE club_membership SET cmStatus = :status WHERE clubMembershipID = :clubMembershipID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':status', $status);
        $query->bindParam(':clubMembershipID', $clubMembershipID);
        
        return $query->execute();
    }
    
    function fetchQuestions($clubID) {
        $sql = "SELECT clubFormQuestionID, cfQuestion FROM club_formquestion WHERE clubID = ?";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(1, $clubID, PDO::PARAM_INT);
        
        if ($query->execute()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return []; // Return an empty array if there's an error
        }
    }
    function updateClubFormQuestion($questionID, $question) {
        $updateSql = "UPDATE club_formquestion SET cfQuestion = :question WHERE clubFormQuestionID = :questionID";
        $updateStmt = $this->db->connect()->prepare($updateSql);
        $updateStmt->bindParam(':question', $question, PDO::PARAM_STR);
        $updateStmt->bindParam(':questionID', $questionID, PDO::PARAM_INT);
        return $updateStmt->execute(); // Return true or false
    }
    
    function insertClubFormQuestion($clubID, $question) {
        $insertSql = "INSERT INTO club_formquestion (clubID, cfQuestion) VALUES (:clubID, :question)";
        $insertStmt = $this->db->connect()->prepare($insertSql);
        $insertStmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
        $insertStmt->bindParam(':question', $question, PDO::PARAM_STR);
        return $insertStmt->execute(); // Return true or false
    }
            

function fetchApplicationDetails($clubMembershipID) {
    $sql = "SELECT u.userImage,
    u.userFirstName, 
    u.userLastName, 
    u.userMiddleName, 
    u.userAge, 
    u.userGender, 
    u.userContactNo, 
    IF(u.userEmail = '', g.guardianEmail, u.userEmail) AS userEmail,
    CONCAT_WS(', ',
        u.userStreetName, 
        NULLIF(u.userBarangay, ''), 
        NULLIF(u.userCity, ''), 
        NULLIF(u.userProvince, ''), 
        NULLIF(u.userZipCode, '')
    ) AS userAddress,
    c.clubName, 
    c.clubDescription,
    cfq.cfQuestion,
    cfa.cfAnswer,
    cm.cmCreatedAt,
    cm.cmStatus
FROM club_membership cm
JOIN user u ON cm.userID = u.userID
LEFT JOIN guardian g ON u.userID = g.userID
JOIN club c ON cm.clubID = c.clubID
JOIN club_formquestion cfq ON cm.clubID = cfq.clubID
JOIN club_formanswer cfa ON cfq.clubFormQuestionID = cfa.clubFormQuestionID
WHERE cm.clubMembershipID = :clubMembershipID
";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':clubMembershipID', $clubMembershipID);

    if ($query->execute()) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return null;
    }
}
function delete($clubFormQuestionID){
    $sql = "DELETE FROM  club_formquestion WHERE clubFormQuestionID = :clubFormQuestionID";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':clubFormQuestionID', $clubFormQuestionID);
    return $query->execute();
}

}

?>