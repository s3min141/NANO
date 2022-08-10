<?php
    include_once($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/sqlLib.php");

    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $userName = $_POST["username"];
        $passWord = $_POST["password"];

        if ($userName == "" || $passWord == "") {
            echo json_encode(array("result"=>"failed", "message"=>"Empty username or password"));
            return;
        }
        
        if (!preg_match('/^[a-zA-Z0-9]+$/', $userName)) {
            echo json_encode(array("result"=>"failed", "message"=>"Invalid username format"));
            return;
        }

        $userInfoResultJson = json_decode(GetUserInfo($userName), true);
        if ($userInfoResultJson["result"] == "success") {
            $userInfoRow = $userInfoResultJson["message"];
            if ($userInfoRow["password"] == AESEncrypt($passWord, $passWord)) {
                /* if ($autoLogin == "true") {
                    setcookie("nn_c_id",$userName,(time()+3600*24*30),"/");
                } */
                $_SESSION["nn_username"] = $userName;
                $_SESSION["nn_userkey"] = $passWord;
                echo json_encode(array("result"=>"success", "message"=>"Successfully logined"));
            }
            else {
                echo json_encode(array("result"=>"failed", "message"=>"Password doesnt match"));
            }
        }
        else {
            echo json_encode(array("result"=>"failed", "message"=>$userInfoResultJson["message"]));
        }
    }
    else {
        echo json_encode(array("result"=>"failed", "message"=>"Invalid parameter"));
    }
?>