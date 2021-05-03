<?php
    require_once('config.php');
    require_once('../model/classes.php');
    session_start(); 
    $current_time = date("Y-n-j H:i:s");
    $maxTime = (!empty($_SESSION['maxTime'] )) ? $_SESSION['maxTime'] : strtotime($current_time);
    $expiration   = round(($maxTime - strtotime($current_time)) / 60);
    if ($expiration <= 0) {
        $expiration = -1;
        session_destroy();
        $obj = array(
            "message"=>'cancelled',
            "expired"=>$expiration,
            "statusText"=>'Time out. If you paid your account will be created in the next hours.'
        );
    }else { 
        try {
            // set PDO CONNECTION
            $connection = new PDO($dbhost, $dbuser, $dbpass);
            $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $ck         = ($_GET['method'] == 'blurt')? new BlurtHandler($connection): new CoinpaymentsHandler($connection);
            $order      = ($_GET['method'] == 'blurt')? $ck->orderExist($_SESSION['txid'], 'memo') : $ck->orderExist($_SESSION['txid']);
            $metadata   = ($_GET['method'] == 'blurt')? $order['metadata'] : $order->getMetadata();
            $status     = ($_GET['method'] == 'blurt')? $order['status'] : $order->getStatus();
            $obj = array(
                "message"    => 'active',
                "expired"    => $expiration,
                "metadata"   => $metadata,
                "statusText" => getStatusMessage($status),
                "status"     => $status
            );
        } catch (Exception $e) {
            echo "on line " . $e->getLine() . " " . $e->getFile() . " ";
            die("Error: " . $e->getMessage());
        }
    }
    
    echo json_encode($obj);
?>