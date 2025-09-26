<?php
    session_start(); // check session
    session_destroy(); // destroy all session data
    
    header('Location: login.php');
    exit;
?>