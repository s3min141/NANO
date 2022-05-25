<?php
    include_once($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/sqlLib.php");

    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_SESSION["nn_username"]) && isset($_POST["behavior"])) {
        $behavior = $_POST["behavior"];
        if ($behavior == "add" && isset($_POST["lecturename"]) && isset($_POST["lecturenum"])) {
            $userName = $_SESSION["nn_username"];
            $lectureName = $_POST["lecturename"];
            $lectureNum = $_POST["lecturenum"];
            
            if ($lectureName != "" || $lectureNum != "") {
                echo AddLecture($userName, $lectureName, $lectureNum);
            }
            else {
                echo json_encode(array("result"=>"failed", "message"=>"Lecture name or Lecture number is empty"));
            }
        }
        else if ($behavior == "del" && isset($_POST["lecturename"])) {
            $userName = $_SESSION["nn_username"];
            $lectureName = $_POST["lecturename"];

            if ($lectureName != "") {
                echo DeleteLecture($userName, $lectureName);
            }
            else{
                echo json_encode(array("result"=>"failed", "message"=>"Unexpected error (Empty lecture name)"));
            }
        }
    }
    else {
        echo json_encode(array("result"=>"failed", "message"=>"Invalid parameter"));
    }
?>