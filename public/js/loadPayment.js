$("#key").val(suggestPassword());
$('.userIsAvailable').click(async (e) => {
    e.preventDefault();
    console.log('Checking...');
    if ($("#username").hasClass('is-valid')) {
        let username = $("#username").val().toLowerCase(); 
        let isValid  = await checkAccountName(username);
        if(isValid) {
            console.log(username + " is available");
            $('.userIsAvailable').addClass('d-none');
            $(".availability").addClass("c-green")
                              .removeClass("d-none")
                              .removeClass("c-red")
                              .html(`<span class="glyphicon glyphicon-ok"></span>Available`);
        }else {
            console.log(username + " is not available");
            $('.userIsAvailable').addClass('d-none');
            $(".availability").addClass("c-red")
                              .removeClass("d-none")
                              .removeClass("c-green")
                              .html(`<span class="glyphicon glyphicon-remove"></span>Unavailable`);
        }
    }else {
        $('.username-error').removeClass('d-none')
                            .html('Please set a valid username first')
    }
});
let userIsAvailable = false;
$('#username').keyup(async function (e) { 
    if ($("#username").hasClass('is-valid')) {
        let username = $("#username").val().toLowerCase(); 
        let isValid  = await checkAccountName(username);
        if(isValid) {
            console.log(username + " is available");
            $('.userIsAvailable').addClass('d-none');
            $(".availability").addClass("c-green")
                              .removeClass("d-none")
                              .removeClass("c-red")
                              .html(`<span class="glyphicon glyphicon-ok"></span>Available`)
                              .attr('data-available','yes');
        }else {
            console.log(username + " is not available");
            $('.userIsAvailable').addClass('d-none');
            $(".availability").addClass("c-red")
                              .removeClass("d-none")
                              .removeClass("c-green")
                              .html(`<span class="glyphicon glyphicon-remove"></span>Unavailable`)
                              .attr('data-available','no');
        }
    }else {
        $('.username-error').removeClass('d-none')
                            .html('Please set a valid username first')
    }
});
$('.generateKeys').click((e) => {
    e.preventDefault();
    let username = $('#username').val();
    if ($('#username').val().match(regx.username)) {
        $("#key").val(suggestPassword());
        let password = $('#key').val();
        generateKeys(username, password);
        $('.key-error').addClass('d-none')
                       .html('');
    }else {
        $('.key-error').removeClass('d-none')
                       .html('Failed generating keys.');
    }
})
$('.downloadKeys').click(async (e) => {
    e.preventDefault();
    const username = $('#username').val().toLowerCase();
    const password = $('#key').val();
    if ($('#username').hasClass('is-valid') && password != '') {
        console.log('Downloading...');
        const keys = generateKeys(username, password);
        const text = `Username: ${username}\n\nMaster password: ${password}\n\nOwner key: ${keys.owner}\n\nActive key: ${keys.active}\n\nPosting key: ${keys.posting}\n\nMemo key: ${keys.memo}`;
        var file = new File([text], `${username}-backup.txt`, { type: "text/plain;charset=utf-8" });
        $('.key-error').addClass('d-none')
                       .html('');
        saveAs(file);
    }else{
        $('.key-error').removeClass('d-none')
                       .html('Failed downloading keys.');
    }
})
$('#create').click(function (e) { 
    let isValid = $('.availability').attr('data-available');
    console.log(isValid);
    if (isValid == undefined) {
        $('#username').focus();
        $('.username-error').removeClass('d-none')
                            .html('Check username availability first.');
        return false;
    }else if (isValid == 'no'){
        $('#username').focus();
        $('.username-error').removeClass('d-none')
                            .html('Username is already use.');
        return false;
    }
});

switch (method) {
    case 'blurt':
        console.log('you selected pay with blurt account');
        (async () => {
            let fee = await getAccountCreationFee();
            fee = '<span class="c-red">' + parseFloat(fee).toFixed(3) + '</span> BLURT';
            $(".chainFee").html(fee);
        })();
        let clicked = false;
        $("#create").click(async (e) => {
            e.preventDefault();
            if (!clicked) {
                const username = $('#username').val().toLowerCase();
                const password = $('#key').val();
                const creator  = $('#creator').val().toLowerCase();
                const transfer = parseFloat($('#gift').val()).toFixed(3);
                const active   = $('#c-key').val();
                const ops = [];
                const fee = await getAccountCreationFee();
                let keys = blurt.auth.generateKeys(username, password, ['owner', 'active', 'posting', 'memo']);
                var owner = { weight_threshold: 1, account_auths: [], key_auths: [[keys.owner, 1]] };
                var activeKey = { weight_threshold: 1, account_auths: [], key_auths: [[keys.active, 1]] };
                var posting = { weight_threshold: 1, account_auths: [], key_auths: [[keys.posting, 1]] };
                // adding the creation transaction to ops
                const create_op = [
                    'account_create',
                    {
                        active: activeKey,
                        creator,
                        extensions: [],
                        fee: fee,
                        json_metadata: '',
                        memo_key: keys.memo,
                        new_account_name: username,
                        owner,
                        posting
                    },
                ];
                ops.push(create_op);
                let transfer_op = (transfer == '')? true:false;
                if (transfer > 0) {
                    //adding the gift on blurt to ops
                    const gift_op = ['transfer', {
                    from: creator,
                    to: username,
                    amount: transfer + " BLURT",
                    memo: ''
                    }];
                    transfer_op = true;
                    ops.push(gift_op);
                }
                if(window.blurt_keychain && active == '') {
                    console.log('Using WhaleVault');
                    blurt_keychain.requestBroadcast(creator, ops, 'active', function (response) {
                        console.log(response); 
                        if (response.success) {
                            $(".wrapper").html(buildFinalMessage(username));
                        } else {
                            alert(`Hey! Some is happened: ${response.message}`)
                        }
                    }); 
                }else {
                    if (active.match(regx.password) && username.match(regx.username) && creator.match(regx.username) && password.match(regx.password)) {
                        clicked = true;
                        $('.send-error').addClass('d-none')
                                    .html('');
                        blurt.broadcast.send(
                            { operations: ops, extensions: [] },
                            { active: active },
                            function (err, result) {
                                clicked = false;
                                if (!err && result) {
                                    console.log(result);
                                    $(".wrapper").html(buildFinalMessage(username));
                                } else {
                                    console.log(err);
                                    $(".wrapper").html(`<div class="container"><div class="row"><div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:35px;">
                                                            <div class="creator-content ta-c">
                                                            <h2>OPS! Failed to create accout.</h2>
                                                            <p>${err.message}</p>
                                                        </div></div></div></div>`);
                            }
                        });
                    }else {
                        $('.send-error').removeClass('d-none')
                                        .html('Fill all fields first.'); 
                    }
                }
                
            } 
           
            
        })
        break;
    case 'card':
        console.log('you selected pay with card'); 
        
        break;

    case 'crypto':
        console.log('you selected pay with cryptos');
        let dollarPrice   = new Promise(async (resolve, reject) => {
            var fee       = await getAccountCreationFee();
            var price     = await getAccountPrice();
            var dollar    = await getCurrencyPrice('blurt') ;
            fee           = parseFloat(fee);
            var blurtRate = JSON.parse(dollar).blurt.usd; 
            dollar        = {rate:JSON.parse(price).price, blurt:{rate:blurtRate, fee:fee}}
            resolve(dollar);
        });
        dollarPrice.then((dollar) => {
            console.log('promise resolved');
            $(".crypto-input").fadeIn(500);
            $("#currency").change(async function () {
                let t        = $(this);
                let currency = t.val();
                console.log(`You will pay with ${currency}`);
                let rate = (currency == 'blurt')? dollar.blurt.rate : $('#option-' + currency).attr('data-rate');
                let title = (currency == 'blurt')?`You will pay a pegged fee of 50.000 BLURT`:`You will pay ${dollar.rate} USD with exchange rate ${rate}`;
                $('.currency-price').addClass('glyphicon-question-sign')
                                    .attr(`title`,title);
                let final = (currency == 'blurt')?/*(dollar.rate / dollar.blurt.rate).toFixed(3)*/50 : parseFloat(rate) * dollar.rate;
                $('#price').val(final);
            });
        });
        
        break;
    default:
        break;
}
