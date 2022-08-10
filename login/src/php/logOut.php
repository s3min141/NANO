<?php
    if (!isset($_SESSION)) {
        session_start();
    }
    
    session_destroy();
    if (isset($_COOKIE["nn_c_id"])) {
        unset($_COOKIE["nn_c_id"]);
        setcookie("nn_c_id", "", time() - 3600, "/"); // empty value and old timestamp
    }
    echo "<script>location.href = '/NANO/login'</script>";
?>