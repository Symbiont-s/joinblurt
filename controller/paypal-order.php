<?php
    require_once('../model/classes.php');
    require_once('config.php');
    session_start();
    if (!empty($_SESSION['token'])) {
        $connection = new PDO($dbhost, $dbuser, $dbpass);
        $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pp         = new PaypalHandler($connection);
        $order      = new Order(); 
        $status     = ($_POST['status'] == 'Kz443A')? 100 : -1;
        $id         = $_POST['id'];
        $order      -> setStatus($status);
        $order      -> setTxId($id);
        if ($order->getTxId() != $_SESSION['txid']) { 
            $resp = $pp -> updateOrder($order, $_SESSION['txid']);
            $_SESSION['txid'] = $order->getTxId();
            if ($resp) {
                echo json_encode(array("success"=>"yes","message"=>"Transacion updated."));
            }else {
                echo json_encode(array("success"=>"no","message"=>"Can't not save the transaction."));
            }
        }
    }else {
        echo json_encode(array("success"=>"no","message"=>"No validation token."));
    }
?>