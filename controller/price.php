<?php
    require_once('config.php');
    require_once('../model/classes.php');
    $connection = new PDO($dbhost, $dbuser, $dbpass);
    $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $h = new Handler($connection);

    $info = $h->getSettings();

    echo json_encode(array("price"=>$info["account_price"]));
?>