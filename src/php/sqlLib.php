<?php
    include_once($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/normalLib.php");
    $con = mysqli_connect("localhost", "id", "pw", "NANO");

    if (!isset($_SESSION)) {
        session_start();
    }
    
    function GetUserInfo($userName) {
        global $con;

        if ($con) {
            $query = mysqli_query($con, "SELECT * FROM userinfo WHERE BINARY username='$userName'");
            if ($query) {
                if (mysqli_num_rows($query) == 1) {
                    $row = mysqli_fetch_assoc($query);
                    return json_encode(array("result"=>"success", "message"=>$row));
                }
                else {
                    return json_encode(array("result"=>"failed", "message"=>"Username doesnt exist"));
                }
            }
            else {
                return json_encode(array("result"=>"failed", "message"=>"Unexpected Error (Query)"));
            }
        }
        else {
            return json_encode(array("result"=>"failed", "message"=>"Unexpected Error (DB)"));
        }
    }

    function GetUserLectureJson($userName) {
        global $con;

        if ($con) {
            $query = mysqli_query($con, "SELECT lecturejson FROM lecturelist WHERE BINARY username='$userName'");
            if ($query) {
                $row = mysqli_fetch_assoc($query);
                
                if ($row == null || $row["lecturejson"] == "") {
                    $userKey = $_SESSION["nn_userkey"];
                    return AESEncrypt("{}", $userKey);;
                }
                return $row["lecturejson"];
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    function AddLecture($userName, $lectureName, $lectureNum) {
        global $con;

        if ($con) {
            $userKey = $_SESSION["nn_userkey"];

            if (!preg_match('/[\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]+/u', $lectureName)) {
                return json_encode(array("result"=>"failed", "message"=>"Invalid lecture name"));
            }

            if (strlen($lectureNum) > 15) {
                return json_encode(array("result"=>"failed", "message"=>"Lecture number is too long"));
            }

            $userLectureJson = AESDecrypt(GetUserLectureJson($userName), $userKey);
            $userLectureJson = json_decode($userLectureJson, true);
            if ($userLectureJson[$lectureName] == null) {
                $userLectureJson[$lectureName] = $lectureNum;
            }
            else {
                return json_encode(array("result"=>"failed", "message"=>"Duplicate lecture name"));
            }

            $jsonToString = json_encode($userLectureJson);
            $jsonToString = AESEncrypt($jsonToString, $userKey);

            $query1 = mysqli_query($con, "SELECT username FROM lecturelist WHERE BINARY username='$userName'");
            if (mysqli_num_rows($query1) == 1) {
                $query2 = mysqli_query($con, "UPDATE lecturelist SET lecturejson='$jsonToString' WHERE BINARY username='$userName'");
                if ($query2) {
                    return json_encode(array("result"=>"success", "message"=>"Succesfully added"));
                }
                else {
                    return json_encode(array("result"=>"failed", "message"=>"Unexpected Error (Query)"));
                }
            }
            else {
                $query3 = mysqli_query($con, "INSERT INTO lecturelist VALUES('$userName', '$jsonToString')");
                if ($query3) {
                    return json_encode(array("result"=>"success", "message"=>"Succesfully added"));
                }
                else {
                    return json_encode(array("result"=>"failed", "message"=>"Unexpected error (Query)"));
                }
            }
        }
        else {
            return json_encode(array("result"=>"failed", "message"=>"Unexpected error (DB)"));
        }
    }

    function DeleteLecture($userName, $lectureName) {
        global $con;

        if ($con) {
            $userKey = $_SESSION["nn_userkey"];

            $userLectureJson = AESDecrypt(GetUserLectureJson($userName), $userKey);
            $userLectureJson = json_decode($userLectureJson, true);
            unset($userLectureJson[$lectureName]);
            $jsonToString = json_encode($userLectureJson);
            $jsonToString = AESEncrypt($jsonToString, $userKey);

            $query = mysqli_query($con, "UPDATE lecturelist SET lecturejson='$jsonToString' WHERE BINARY username='$userName'");
            if ($query) {
                return json_encode(array("result"=>"success", "message"=>"Succesfully deleted"));
            }
            else {
                return json_encode(array("result"=>"failed", "message"=>"Unexpected Error (Query)"));
            }
        }
        else {
            return json_encode(array("result"=>"failed", "message"=>"Unexpected error (DB)"));
        }
    }

    function RegisterUser($userName, $passWord) {
        global $con;

        if ($con) {
            $passWord = AESEncrypt($passWord, $passWord);
            $query = mysqli_query($con, "SELECT username FROM userinfo WHERE username='$userName'");
            if ($query) {
                if (mysqli_num_rows($query) == 0) {
                    $query = mysqli_query($con, "INSERT INTO userinfo VALUES('$userName', '$passWord')");
                    if ($query) {
                        return json_encode(array("result"=>"success", "message"=>"Succesfully registered"));
                    }
                    else {
                        return json_encode(array("result"=>"failed", "message"=>"Unexpected Error (Query)"));
                    }
                }
                else {
                    return json_encode(array("result"=>"failed", "message"=>"User already exist"));
                }
            }
            else {
                return json_encode(array("result"=>"failed", "message"=>"Unexpected Error (Query)"));
            }
        }
        else {
            return json_encode(array("result"=>"failed", "message"=>"Unexpected error (DB)"));
        }
    }
?>
