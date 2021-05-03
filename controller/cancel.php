<?php
    // this file close the open orders
    session_start();
    if (!empty($_SESSION['method'])) {
        if ($_SESSION['method'] == 'coinpayments') {
            session_destroy();
        }else if ($_SESSION['method'] == 'blurt') {
            session_destroy();
        }else if ($_SESSION['method'] == 'paypal'){
            require_once('../model/classes.php');
            require_once('config.php');
            $connection = new PDO($dbhost, $dbuser, $dbpass);
            $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pp         = new PaypalHandler($connection);
            $order      = new Order();
            $cOrder     = $pp->orderExist($_SESSION['txid']);
            if($cOrder['status'] == 0){
                $order      -> setStatus(-1);
                $order      -> setTxId($_SESSION['txid']);
                $resp       = $pp -> updateOrder($order, $_SESSION['txid']);
            } 
            session_destroy();
        }
    }
    
    
    header('location:.././');
?>