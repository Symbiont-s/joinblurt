<?php 
    $_MAIN = false; 
    include('../controller/createOrder.php'); 
?>
<!DOCTYPE html>
<html lang="en-EN">
<head>
    <?php include('templates/libraries.php') ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="Joinblurt" />
    <meta http-equiv="default-style" content="default-stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="IE=7, chrome=1, firefox=1, opera=1, ie=edge">
    <meta name="title" content="Pay with">
    <meta name="description" content="Process your payment.">
    <meta name="keywords" content="blurt, symbionts, blockchain, accounts, account creator, paypal, crypto">
    <meta name="encoding" charset="utf-8" />
    <meta name="author" content="Symbionts Team">
    <meta name="copyright" content="Symbionts">
    <meta name="robots" content="index, follow"/>
    <script src='./public/js/utils.js'></script>
    <title>Join Blurt</title>
</head>
<body>
    <header>           
    </header> 
    <section class='wrapper'>
        <div class="container">
            <div class="row">
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:35px;">
                    <div class="creator-content ta-c">
                        
                        <?php 
                            if ($_GET['method'] == 'crypto') { 
                                if ($order->getCurrency() == 'blurt') { 
                        ?>
                        <h2>Blurt Payment. Please do no close this page.</h2>
                        <p>
                            <b>Deposit Username</b><br>
                            <span><?php echo $info['creator']; ?></span><br>
                            <b>Deposit Memo</b><br>
                            <span><?php echo $order->getDepositAddress(); ?></span><br>
                            <b>Price</b><br>
                            <span><?php echo $order->getAmount(); ?> BLURT</span><br>
                            <b>Time Window</b><br>
                            <span>Your order will expire in <b class='expiration'><?php $current_time = date("Y-n-j H:i:s"); echo round(($_SESSION['maxTime'] - strtotime($current_time)) / 60); ?></b> minutes.</span><br>
                            <b>Status</b><br>
                            <span class='order-status' order-method='blurt' order-id='<?php echo $_SESSION['txid']; ?>'><?php echo getStatusMessage($order->getStatus()); ?></span><br>
                            <b>Instructions</b><br>
                            <span>Use your BLURT wallet and send the amount to the deposit account and make sure to also include the deposit memo mentioned above.</span>

                        </p>
                        <?php }else{ ?>
                        <h2>Order Created. Please do no close this page.</h2> 
                        <p>
                            <b>Transaction ID</b><br>
                            <span><?php echo $cOrder->getTxId(); ?></span><br>
                            <b>Deposit Address</b><br>
                            <span><?php echo $cOrder->getDepositAddress(); ?></span><br>
                            <b>Amount</b><br>
                            <span><?php echo $cOrder->getAmount() . ' ' . $cOrder->getCurrency(); ?></span><br>
                            <b>Time Window</b><br>
                            <span>Your order will expire in <b class='expiration'><?php $current_time = date("Y-n-j H:i:s"); echo ($_SESSION['maxTime'] - strtotime($current_time)) / 60; ?></b> minutes.</span><br>
                            <b>Status</b><br>
                            <span class='order-status' order-method='crypto' order-id='<?php echo $cOrder->getId(); ?>'><?php echo getStatusMessage($cOrder->getStatus()); ?></span><br>
                            <?php if(!empty($cOrder->getQr())) { ?>
                                <div class='qr-info'>
                                    Deposit Address QR Code
                                    <img src="<?php echo $cOrder->getQr(); ?>" style='width:100px;'>
                                </div>
                            <?php }} ?>
                        </p>    
                        <?php }else if ($_GET['method'] == 'card') { if (count($seller) > 0) { ?>
                            <h2 class='pp-title'>Order Created. Please do no close this page.</h2>
                            <?php if($order->getStatus() == 0) { ?>
                            <div id="smart-button-container">
                                <div style="text-align: center;">
                                    <div id="paypal-button-container" data-price="<?php echo $order->getAmount(); ?>"></div>
                                </div>
                            </div>
                            <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $seller['client']; ?>&currency=USD" data-sdk-integration-source="button-factory"></script>
                            <script src='./public/js/paypal.js'></script>
                            <?php } ?>
                            <p>
                                <b>Status</b><br>
                                <span class='order-status' order-method='card'><?php echo getStatusMessage($order->getStatus()); ?></span><br>
                                <b>Price</b><br>
                                <span><?php echo $order->getAmount() . ' ' . $order->getCurrency(); ?></span><br>
                                <b>Time Window</b><br>
                                <span>Your order will expire in <b class='expiration'><?php $current_time = date("Y-n-j H:i:s"); echo round(($_SESSION['maxTime'] - strtotime($current_time)) / 60); ?></b> minutes.</span><br>
                                <div class="feedback">
                                </div> 
                            </p>
                        <?php
                                }else{ 
                                    echo "<h2>This is unavailable now, try later.</h2>";
                                } 
                            }
                        ?>  
                    </div>
                </div>    
            </div>
            <?php if(!empty($_SESSION['maxTime'])) { ?> 
                <div class="row">
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:10px;"> 
                            <button class="cancelOrder btn btn-cancel" name="create" id="create">Cancel Order</button>
                    </div>
                </div> 
            <?php } ?>
                <div class="row">
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:10px;"> 
                            <a href="./" class="btn btn-back" style='display:none;'>Go Back</a>
                    </div>
                </div> 
        </div>
    </section>
    <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0;URL=./noJS">
    </noscript>
    
    <?php if(!empty($_SESSION['maxTime'])) { echo "<script src='./public/js/timeChecker.js'></script>"; }?>
    <?php include('templates/footer.php'); ?>
</body>
</html>
