<?php
define("DB_HOST", "localhost");
define("DB_NAME", "db_noair");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_PORT", "3306"); //change this if your port is different

<<<<<<< HEAD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
=======
// Session cookie expires when browser closes (not a persistent cookie)
ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 3600); // server-side session data kept for 1 hour max
session_start();

// auto-login from remember-me cookie if not already in session
require_once $_SERVER['DOCUMENT_ROOT'] . "/logic/auth_helper.php";
checkRememberMeCookie();

>>>>>>> main
