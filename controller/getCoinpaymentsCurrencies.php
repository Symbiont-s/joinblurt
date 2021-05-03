<?php 
    require_once('config.php');
    require_once('../model/classes.php');
    try {
        // set PDO CONNECTION
        $connection = new PDO($dbhost, $dbuser, $dbpass);
        $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $ck   = new CoinpaymentsHandler($connection);
        $currencies = $ck->getAcceptedCurrencies();
    } catch (Exception $e) {
        echo "on line " . $e->getLine() . " " . $e->getFile() . " ";
        die("Error: " . $e->getMessage());
    }
    
?>