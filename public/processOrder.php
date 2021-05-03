<?php 
    $_MAIN = false;
    session_start();
    include('../controller/checkOrder.php');
    if (isset($_SESSION['txid'])) {
        header('location:./orders/active');
    }
?>
<!DOCTYPE html>
<html lang="en-EN">
<head>
    <?php include('templates/libraries.php') ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="Joinblurt" />
    <meta http-equiv="default-style" content="default-stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="IE=7, chrome=1, firefox=1, opera=1, ie=edge">
    <meta name="title" content="Choose your payment method."> 
    <meta name="description" content="Choose your payment method.">
    <meta name="keywords" content="blurt, symbionts, blockchain, accounts, account creator, paypal, crypto">
    <meta name="encoding" charset="utf-8" />
    <meta name="author" content="Symbionts Team">
    <meta name="copyright" content="Symbionts">
    <meta name="robots" content="index, follow"/>
    <title>Join Blurt</title>
</head>
<body>
    <header>
        <?php
            echo (isset($_GET['method']))? "<div class='pm-method d-none' data-id='" . $_GET['method'] . "'></div>":
                                           "<div class='pm-method d-none' data-id='blurt'></div>";
        ?>
    </header> 
    <section class='wrapper' style="margin-top:30px;">
        <form action="./<?php echo $_GET['method']; ?>/pay" method="post" name='payment-with-<?php echo $_GET['method'];?>' id='payment-with-<?php echo $_GET['method'];?>'> 
            <div class="container">
                <div class="row">
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="username">Desired Account Name</label>
                            <input type="text" class="form-control" name="username" id="username">
                            <div class="info-section ta-r pt-2"><span class="username-error c-red d-none">ERROR TEST</span><span class="gly-icon glyphicon glyphicon-user"></span><div class="availability d-none"></div><a class="userIsAvailable btn-filter ml-4">Check Availability</a></div>
                        </div>
                    </div>
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="key">Master Key</label>
                            <input type="text" class="form-control" name="key" id="key">
                            <div class="position-relative">
                                <label for="posting-key">Posting Key</label>
                                <input type="text" class="form-control" name="posting-key" id="posting-key" readonly>
                                <span class="gly-icon-key glyphicon glyphicon glyphicon-lock"></span>
                            </div>
                            <div class="position-relative">
                                <label for="active-key">Active Key</label>
                                <input type="text" class="form-control" name="active-key" id="active-key" readonly>
                                <span class="gly-icon-key glyphicon glyphicon glyphicon-lock"></span>
                            </div>
                            <div class="position-relative">
                                <label for="owner-key">Owner Key</label>
                                <input type="text" class="form-control" name="owner-key" id="owner-key" readonly>
                                <span class="gly-icon-key glyphicon glyphicon glyphicon-lock"></span>
                            </div>
                            <div class="position-relative">
                                <label for="memo-key">Memo Key</label>
                                <input type="text" class="form-control" name="memo-key" id="memo-key" readonly>
                                <span class="gly-icon-key glyphicon glyphicon glyphicon-lock"></span>
                            </div>
                            <div class="info-section ta-r pt-2"><span class="key-error c-red d-none">ERROR TEST</span><span class="gly-icon glyphicon glyphicon-lock"></span><a class="btn-filter generateKeys ml-4">Generate New Keys</a><a class="btn-filter downloadKeys ml-4">Download Backup</a></div>
                        </div>
                    </div>
                </div>
        <?php 
            switch ($_GET['method']) {
                case 'blurt':
        ?>
        
                <div class="row">
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="creator">Account Creator</label>
                            <input type="text" class="form-control" name="creator" id="creator">
                            <div class="info-section ta-r pt-2"><span class="creator-error c-red d-none">ERROR TEST</span><span class="gly-icon glyphicon glyphicon-user"></span> Fee: <span class="chainFee"></span></div>
                        </div>
                    </div>
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="c-key">Account Creator Active Key</label>
                            <input type="text" class="form-control" name="c-key" id="c-key">
                            <div class="info-section ta-r pt-2"><span>* Leave empty to use WhaleVault</span></div><span class="c-key-error c-red d-none">ERROR TEST</span><span class="gly-icon glyphicon glyphicon-lock"></span>
                        </div>
                    </div>
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="gift">Optional - Gift BLURT</label>
                            <input type="text" class="form-control" name="gift" id="gift">
                            <span class="gift-error c-red d-none">ERROR TEST</span><span class="gly-icon glyphicon glyphicon-gift"></span>
                        </div> 
                    </div> 
                </div>       
        <?php
                    break;
                case "card":
                    
        ?>
             <div class="row" >
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                    <div class="creator-content">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                        <div class="info-section ta-r pt-2"><span>* You will be notifed after the account creation</span><span class="gly-icon glyphicon glyphicon-envelope"></span></div>
                    </div>
                </div>
            </div>
            
        <?php
                
                    break;
                case "crypto":
                    include('../controller/getCoinpaymentsCurrencies.php'); 
        ?>
                <div class="row crypto-input" style='display:none;'>
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="currency">Currency</label>
                            <select class='form-control' name="currency" id="currency">
                                <option value="">--SELECT--</option>
                                <option value="blurt" id="option-blurt">BLURT</option>
                            <?php
                                foreach($currencies as $c){
                                    echo "<option id='option-" . $c['currency'] . "' value='" . $c['currency'] . "' data-rate='" . $c['rate'] . "'>" . $c['currency'] . "</option>";
                                }
                            ?>
                            </select>
                            <div class="info-section ta-r pt-2"><span>* Use BLURT and benefit from a lower price.</span></div>
                        </div> 
                    </div>
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="price">Price</label>
                            <input type="text" class="form-control" name="price" id="price" readonly>
                            <div class="info-section ta-r pt-2"><span class="gly-icon currency-price glyphicon "></span></div>
                        </div>
                    </div>
                    <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;">
                        <div class="creator-content">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                            <div class="info-section ta-r pt-2"><span>* You will be notifed after the account creation.</span><span class="gly-icon glyphicon glyphicon-envelope"></span></div>
                        </div>
                    </div>
                </div>
        <?php
                    break;
                default:
                    break;
            } 
        ?>
            <div class="row">
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;"> 
                    <div class="mt-3">
                        <button class="btn btn-send" name="create" id="create">Create Account</button>
                        <center><span class="send-error c-red d-none">ERROR TEST</span></center>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3" style="margin-top:10px;"> 
                        <a href="./" class="btn btn-back">Go Back</a>
                </div>
            </div>
            </div>
        </form> 
    </section> 
    <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0;URL=./noJS">
    </noscript>
    <br><br><br>
    <?php include('templates/footer.php'); ?>
    <script src="./public/js/utils.js"></script>
    <script src="./public/js/validator.js"></script>
    <script src="./public/js/loadPayment.js"></script>
</body>
</html>
