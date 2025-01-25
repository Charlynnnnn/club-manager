<?php

require_once 'database.php';

Class User{
    //attributes

    public $userID;
    public $libraryAttendanceID;
    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function fetch($record_id){
        $sql = "SELECT * FROM user WHERE userID = :userID;";
        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $record_id);
        if($query->execute()){
            $data = $query->fetch();
        }
        return $data;
    }

    function show(){
        $sql = "SELECT * FROM user ORDER BY userLastName ASC, userFirstName ASC;";
        $query=$this->db->connect()->prepare($sql);
        $data = null;
        if($query->execute()){
            $data = $query->fetchAll();
        }
        return $data;
    }
    function getAttendanceDetails($userID) {
        $sql = "SELECT lib_attendanceuser.*, library_attendance.libraryAttendanceID, library_attendance.attendanceCheckerID, u.userFirstName, u.userMiddleName, u.userLastName, u.userAge, u.userGender, u.userContactNo, u.userSchoolOffice, u.userRegion, u.userProvince, u.userCity, u.userBarangay, u.userStreetName, u.userZipCode, ac.acFirstName, ac.acMiddleName, ac.acLastName
        FROM lib_attendanceuser
         JOIN library_attendance ON lib_attendanceuser.libraryAttendanceID = library_attendance.libraryAttendanceID
         JOIN attendance_checker ac ON library_attendance.attendanceCheckerID = ac.attendanceCheckerID
         JOIN user u ON lib_attendanceuser.userID = u.userID
        WHERE lib_attendanceuser.userID = :userID
        ";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $userID);
        if ($query->execute()) {
            $data = $query->fetchAll();
            return $data;
        } else {
            // Handle errors here
            return null;
        }
    }
}

?>