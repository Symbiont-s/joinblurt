<?php 
/**
 * Handle all new orders and save in the database
 * supported
 * -PayPal
 * -Coinpayments
 */
    require_once('config.php');
    require_once('../model/classes.php');
    $connection = new PDO($dbhost, $dbuser, $dbpass);
    $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();
    $pp    = new PaypalHandler($connection);
    $order = new Order();
    $h     = new BlurtHandler($connection);
    $ck    = new CoinpaymentsHandler($connection);
    $info  = $h->getSettings(); 

    //comparing url to always get correct url
    if (!empty($_SESSION['method'])) {
        if ($_SESSION['method'] == 'coinpayments' && $_GET['method'] != 'crypto') {
            header("location:.././crypto/pay");
        }else if ($_SESSION['method'] == 'paypal' && $_GET['method'] != 'card') {
            header("location:.././card/pay");
        }else if ($_SESSION['method'] == 'blurt' && $_GET['method'] != 'crypto') {
            header("location:.././crypto/pay");
        }
    }
    

    if (!empty($_SESSION['txid']) && $_SESSION['method'] == 'coinpayments') {
        // coinpayments order is active
        header('location:../orders/active');
    }else if (!empty($_SESSION['txid']) && $_SESSION['method'] == 'paypal') {
        // paypal order is active 
        $cOrder     = $pp -> orderExist($_SESSION['txid']);
        $order      -> setAmount($info['account_price']);
        $order      -> setCurrency('USD');
        $order      -> setStatus($cOrder['status']);
        $seller     = $pp->getClientID();
    }else if (!empty($_SESSION['txid']) && $_SESSION['method'] == 'blurt') {
        // paypal order is active 
        $cOrder     = $h -> orderExist($_SESSION['txid'], 'memo');
        $order      -> setDepositAddress($cOrder['memo']);
        $order      -> setTxId($cOrder['memo']);
        $order      -> setAmount($cOrder['amount']);
        $order      -> setCurrency('blurt');
        $order      -> setStatus($cOrder['status']); 
    }else if (isset($_POST['create']) && empty($_SESSION['txid'])) {
        //creating new order
        try { 
            $account_metadata = json_encode(array(
                'username'   => strtolower($_POST['username']),
                'master_key' => $_POST['key']
            )); 
            if ($_GET['method'] == 'crypto') {
                if ($_POST['currency'] == 'blurt') { 
                    $order -> setCurrency('blurt');
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                    $order -> setDepositAddress('CRE' . substr(str_shuffle($permitted_chars), 0, 13));
                    $order -> setStatus(0);
                    $order -> setAmount(htmlentities(addslashes($_POST['price']), ENT_QUOTES));
                    $order -> setBuyerEmail(htmlentities(addslashes($_POST['email']), ENT_QUOTES));
                    $h     -> createOrder($order, $account_metadata);
                    $_SESSION['txid']    = $order->getDepositAddress();
                    $_SESSION['created'] = date('Y-n-j H:i:s');
                    $_SESSION['method']  = 'blurt'; 
                    $_SESSION['maxTime'] = strtotime($_SESSION['created']) + 5400; 
                }else { 
                    $order -> setAmount(htmlentities(addslashes($_POST['price']), ENT_QUOTES));
                    $order -> setCurrency(htmlentities(addslashes($_POST['currency']), ENT_QUOTES));
                    $order -> setBuyerEmail(htmlentities(addslashes($_POST['email']), ENT_QUOTES));
                    $cOrder = $ck->createOrder($order, $account_metadata);

                    //saving session data
                    $_SESSION['txid']    = $cOrder->getTxId();
                    $_SESSION['created'] = date('Y-n-j H:i:s');
                    $_SESSION['method']  = 'coinpayments';
                    // getTimeOut get the expiration time provide by coinpayments
                    $_SESSION['maxTime'] = strtotime($_SESSION['created']) + $cOrder->getTimeOut(); 
                    $_SESSION['qr']      = $cOrder->getQr();
                } 
            }else if ($_GET['method'] == 'card'){ 
                $seller = $pp->getClientID();
                if (count($seller) > 0) {
                    $order  -> setAmount($info['account_price']);
                    // $order  -> setAmount(0.1);
                    $order  -> setCurrency('USD');
                    $order  -> setBuyerEmail(htmlentities(addslashes($_POST['email']), ENT_QUOTES));
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                    $order  -> setTxId('PAYP' . substr(str_shuffle($permitted_chars), 0, 10));
                    $order  -> setMethod('paypal');
                    $order  -> setStatus(0);
                    $order  = $pp -> createOrder($order, $account_metadata);
                    // saving session data
                    $_SESSION['txid']    = $order->getTxId();
                    $_SESSION['method']  = 'paypal';
                    $_SESSION['created'] = date('Y-n-j H:i:s');
                    $_SESSION['token']   = 'TOK' . substr(str_shuffle($permitted_chars), 0, 16);
                    $_SESSION['maxTime'] = strtotime($_SESSION['created']) + 3600; 
                } 
            }
        } catch (Exception $e) {
            echo "on line " . $e->getLine() . " " . $e->getFile() . " ";
            die("Error: " . $e->getMessage());
        }
    }else{
        header('location:.././');
    }
    
?>