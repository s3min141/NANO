<?php
    class UserController extends Controller
    {
        protected $sql;

        function __construct()
        {
            global $sql;
            $this->sql = $sql;
        }

        public function LogoutAction()
        {
            if (!$this->IsSigned()) {
                $this->Output(false);
            }

            $_SESSION = [];
            session_destroy();
            $this->Output(true);
        }

        public function LoginAction()
        {
            $input_userName = $this->AuthFilter($_POST["username"]);
            $input_passWord = $this->AuthFilter($_POST["password"]);
            $addr = $this->AuthFilter($_SERVER["REMOTE_ADDR"]);
            if ($input_userName == "" || $input_passWord == "") {
                $this->Output(false);
            }

            $userInfo = new UserInfo;
            $conditionArr = ["username"=>$input_userName];
            $userModel = $userInfo->Get([], $conditionArr, 1);
            if (!$userModel) {
                $this->Output(false);
            }

            if ($userModel->password != SecureHash($input_passWord)) {
                $this->Output(false);
            }

            $_SESSION["username"] = $userModel->username;
            $_SESSION["session"] = SecureHash($userModel->username . $addr);
            $_SESSION["privatekey"] = SecureHash($userModel->password);
            $_SESSION["bbpassword"] = $userModel->bbpassword;
            $this->Output(true);
        }

        public function RegisterAction()
        {
            $input_userName = $this->AuthFilter($_POST["username"]);
            $input_passWord = $this->AuthFilter($_POST["password"]);
            $input_email = $this->AuthFilter($_POST["email"]);
            if ($input_userName == "" || $input_passWord == "" || $input_email == "") {
                $this->Output(false);
            }
            
            $userInfo = new UserInfo;
            $conditionArr = ["username"=>$input_userName];
            $userModel = $userInfo->Get([], $conditionArr, 1);
            if ($userModel) {
                $this->Output(false);
            }

            $userModel = new User;
            $userModel->uuid = GenerateUUID();
            $userModel->email = $input_email;
            $userModel->username = $input_userName;
            $userModel->password = SecureHash($input_passWord);
            $userModel->bbpassword = AESEncrypt($input_passWord, SecureHash($userModel->password)); //encrypt with privatekey

            $registerResult = $userInfo->Set($userModel);
            $this->Output($registerResult);
        }

        public function ProfileAction()
        {
            if (!$this->IsSigned()) {
                $this->Output(false);
            }

            $userName = $this->AuthFilter($_SESSION["username"]);
            $userInfo = new UserInfo;
            $conditionArr = ["username"=>$userName];
            $userModel = $userInfo->Get(["uuid", "username"], $conditionArr, 1);
            if (!$userModel) {
                $this->Output(false);
            }

            $userJson = Array("uuid"=>$userModel->uuid, "username"=>$userModel->username);
            $this->Output($userJson);
        }
    }
?>