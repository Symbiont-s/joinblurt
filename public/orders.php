<?php 
    $_MAIN = false;
    include('../controller/checkOrder.php');
    include('../controller/getTxStatus.php'); 
?>
<!DOCTYPE html>
<html lang="en-EN">
<head>
    <?php include('templates/libraries.php') ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="Joinblurt" />
    <meta http-equiv="default-style" content="default-stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="IE=7, chrome=1, firefox=1, opera=1, ie=edge">
    <meta name="title" content="Pay Your order">
    <meta name="description" content="Process your payment or see your order details.">
    <meta name="keywords" content="blurt, symbionts, blockchain, accounts, account creator, paypal, crypto">
    <meta name="encoding" charset="utf-8" />
    <meta name="author" content="Symbionts Team">
    <meta name="copyright" content="Symbionts">
    <meta name="robots" content="index, follow"/>
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
                        <?php if(!$exist) { ?>
                        <h2>Ops! your order no exist.</h2> 
                        <?php }else{ ?>
                            <b>Order ID</b><br>
                            <span><?php echo $order->getTxId(); ?></span><br>
                            <?php if ($order->getStatus() != 200 && $order->getStatus() >= 0) { ?>
                            <b>Deposit Address</b><br>
                            <span><?php echo $order->getDepositAddress(); ?></span><br>
                            <b>Amount</b><br>
                            <span><?php echo $order->getAmount() . ' ' . $order->getCurrency(); ?></span><br>
                            <?php } ?>
                            <?php if($_GET['txid'] == 'active') { ?>
                                <b>Creation Time</b><br>
                                <span>Your order will expire on <b class='expiration'><?php $current_time = date("Y-n-j H:i:s"); echo round(($_SESSION['maxTime'] - strtotime($current_time)) / 60); ?></b> minutes.</span><br>
                            <?php } ?>
                            <b>Status</b><br>
                            <span class='order-status' order-method='crypto' order-id='<?php echo $order->getId(); ?>'><?php echo getStatusMessage($order->getStatus()); ?></span><br>
                            <?php if (!empty($_SESSION['qr'])) { ?>
                            <div class="qr-info">
                                Deposit Address QR Code
                                <img src="<?php echo $_SESSION['qr']; ?>" style='width:100px;'>
                            </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div> 
            <?php if($_GET['txid'] == 'active') { ?> 
            <div class="row">
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:10px;"> 
                        <button class="cancelOrder btn btn-cancel">Cancel Order</button>
                </div>
            </div>  
            <?php } ?>
            <div class="row">
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:10px;"> 
                        <a href="./" class="btn btn-back">Go Back</a>
                </div>
            </div> 
        </div>
    </section>
    <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0;URL=./noJS">
    </noscript>
    <script src='./public/js/utils.js'></script>
    <?php if($_GET['txid'] == 'active') { echo "<script src='./public/js/timeChecker.js'></script>"; }?>
    <?php include('templates/footer.php'); ?>
</body>
</html>
