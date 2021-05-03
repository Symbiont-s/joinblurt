<?php 
    $_MAIN = true;
    include('../controller/checkOrder.php');
?>
<!DOCTYPE html>
<html lang="en-EN">
<head>
    <?php include('templates/libraries.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="Joinblurt" />
    <meta http-equiv="default-style" content="default-stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="IE=7, chrome=1, firefox=1, opera=1, ie=edge">
    <meta name="title" content="Main Page">
    <meta name="description" content="Account Creator for the Blurt Blockchain. By Symbionts">
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
                <div class="col-12 ta-c">
                    <h1 style='font-size:24px;'>Creat a Blurt Account</h1>
                </div> 
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-0 col-lg-4 offset-lg-0 col-xl-4 offset-xl-0" style="margin-top:20px;">
                    <div class="blurt-content main-content ta-c">
                        <img src="./public/img/blurt-logo.png" class="icon" title="Blurt Logo">
                        <p class='mt-2'>
                        Use your Blurt Account to create a new one. <br>
                        <div class="creator-content mt-1"><span style='color:black;'>Blockchain Fee: <span class='fee'></span></span></div> 
                        </p>
                    </div>
                    <div class="mt-2">
                        <a href="./blurt" class="btn btn-create">Sign Up</a>
                    </div>
                </div>
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-0 col-lg-4 offset-lg-0 col-xl-4 offset-xl-0" style="margin-top:20px;">
                    <div class="blurt-content main-content ta-c">
                        <img src="./public/img/card.png" class="icon" title="Card Logo">
                        <p>
                        Pay with your Debit/Credit Card or PayPal. <br>
                        <div class="creator-content mt-1"><span class='prices' style='color:black;'>Loading...</span></div>
                        </p>
                    </div>
                    <div class="mt-2">
                        <a href="./card" class="btn btn-create">Sign Up</a>
                    </div>
                </div>
                <div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-0 col-lg-4 offset-lg-0 col-xl-4 offset-xl-0" style="margin-top:20px;">
                    <div class="blurt-content main-content ta-c">
                        <img src="./public/img/crypto.png" class="icon" title="Crypto Logo">
                        <p>
                        Pay with crypto to create a new account. <br>
                        <div class="creator-content mt-1"><span class="prices" style='color:black;'>Loading...</span></div>
                        </p>
                    </div>
                    <div class="mt-2">
                        <a href="./crypto" class="btn btn-create">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    
    </section>
    <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0;URL=./noJS">
    </noscript>
    <br><br><br>
    <script src="./public/js/utils.js"></script>
    <script src="./public/js/getPrices.js"></script>
    <?php include('templates/footer.php'); ?>
</body>
</html>
