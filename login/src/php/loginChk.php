<?php
    include_once($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/sqlLib.php");
    
    if (!isset($_SESSION)) {
        session_start();
    }

    /* if (isset($_COOKIE["nn_c_id"])) {
        $c_id = $_COOKIE["nn_c_id"];
        if ($c_id) {
            $resultJson = json_decode(GetUserInfo($c_id), true);
            $userInfoRow = $resultJson["message"];
            
            $_SESSION["nn_username"] = $c_id;
            $_SESSION["nn_userkey"] = $userInfoRow["password"];
        }
    } */
    
    $nowPath = $_SERVER['REQUEST_URI'];
    if (isset($_SESSION["nn_username"])) {
        if ($nowPath != "/NANO/") {
            if ($nowPath == "/NANO/register/") {
                if ($_SESSION["nn_username"] != "minse0204") {
                    echo "<script>location.href = '/NANO';</script>";
                }
            }
            else {
                echo "<script>location.href = '/NANO';</script>";
            }
        }
    }
    else {
        if ($nowPath != "/NANO/login/") {
            echo "<script>location.href = '/NANO/login';</script>";
        }
    }
?>