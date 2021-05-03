<?php
    
    if (!empty($_SESSION["txid"])) { 
        # validating time since the creation
        $max      = $_SESSION["maxTime"];
        $current  = date("Y-n-j H:i:s");
        $time     = ($max-strtotime($current));
       
        //if session inactive time is 30 minutes close session
        if ($time<=0) {
            session_destroy();
            header("location:./");
        } 
    }
?>