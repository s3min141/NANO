<?php
    include($_SERVER["DOCUMENT_ROOT"] . "/lib/externallib/php-webdriver/vendor/autoload.php");
    include($_SERVER["DOCUMENT_ROOT"] . "/lib/externallib/simplehtmldom.php");
    
    displayError(false);
    $sess_allowed_sec = 3600 * 24 * 7; // 7 days
    ini_set("session.gc_maxlifetime", $sess_allowed_sec);
    session_set_cookie_params($sess_allowed_sec);
    session_cache_expire($sess_allowed_sec);
    session_name("session_name");
    session_start();
    
    // DB Credentials (MySQL)
    define("__DB_HOST__", "localhost");
    define("__DB_USER__", "id");
    define("__DB_PASS__", "pw");
    define("__DB_BASE__", "db_name");

    // Base URL //
    define("__HOST__", "https://" . $_SERVER['SERVER_NAME']);

    // Hash Salt //
    define("__HASH_SALT__", "hash value");

    // Template Dir //
    define("__TEMPLATE__", $_SERVER['DOCUMENT_ROOT'] . "/template/");

    require_once "sql.php";
    $sql = new SQL();
    $sql->ConnectDB(__DB_HOST__, __DB_USER__, __DB_PASS__, __DB_BASE__);
    if (!$sql->CheckConn()) {
        die("DB is down");
    }
?>