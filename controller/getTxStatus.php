<?php
/**
 * Return the data from a explicit order
 */
    require_once('config.php');
    require_once('../model/classes.php');
    $exist = false; 
    session_start();
    if (isset($_GET['txid']) || isset($_SESSION['txid'])) {
        # checking the txid
        try {
            // set PDO CONNECTION
            $connection = new PDO($dbhost, $dbuser, $dbpass);
            $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($_SESSION['method'] == 'coinpayments') {
                $ck = new CoinpaymentsHandler($connection);
                $tx = $_GET['txid'];
                if ($_GET['txid'] == 'active' && !empty($_SESSION['txid'])) {
                    $tx = $_SESSION['txid'];//giving prority to open orders
                }else if ($_GET['txid'] == 'active' && empty($_SESSION['txid'])){
                    header('location:.././');
                }
                $order = $ck->orderExist($tx);
                if (!$order) {
                    $exist = false;
                }else {
                    $exist = true; 
                }
                if ($order->getStatus() == 200 && !empty($_SESSION['txid'])) {
                    header('location:.././action/cancel');
                }
            }else if($_SESSION['method'] == 'blurt' || $_SESSION['method'] == 'paypal'){
                $page = ($_SESSION['method'] == 'blurt')? 'crypto' : 'card';
                header('location:.././' . $page . '/pay');
            } 
        } catch (Exception $e) {
            echo "on line " . $e->getLine() . " " . $e->getFile() . " ";
            die("Error: " . $e->getMessage());
        }
    }else{
        header('location:.././');
    }
?>