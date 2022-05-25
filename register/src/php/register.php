<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

    include_once($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/sqlLib.php");

    if (isset($_SESSION["nn_username"]) && isset($_POST["username"]) && isset($_POST["password"])) {
        if ($_SESSION["nn_username"] == "admin id") {
            $userName = $_POST["username"];
            $passWord = $_POST["password"];
            
            if ($userName == "" || $passWord == "") {
                return json_encode(array("result"=>"failed", "message"=>"Empty username or password"));
            }

            $registerResultJson = RegisterUser($userName, $passWord);
            echo $registerResultJson;
        }
        else {
            echo json_encode(array("result"=>"failed", "message"=>"Not admin"));
        }
    }
?>