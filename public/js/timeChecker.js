
let paymentMethod = $('.order-status').attr('order-method'); 
let liveHandle = setInterval(() => { 
    $.ajax({
        method: "GET",
        url: "./action/check?method=" + paymentMethod, 
        dataType: "json",
        success: function (response) { 
            $('.expiration').html(response.expired);
            $('.order-status').html(response.statusText);
            if (response.expired <= 0) {
                $('.qr-info').hide();
                $('.cancelOrder').hide();
                $('.btn-back').show(); 
            }
            if (response.status == 200 || response.status == 100) {
                $('.cancelOrder').hide();
                $('.btn-back').attr('data-status', '200'); 
                $('.btn-back').show(); 
                if (response.status == 200) {
                    let meta = JSON.parse(response.metadata);
                    $(".wrapper").html(buildFinalMessage(meta.username));
                }
            }
        }
    });
}, 1000);
$('.btn-back').click(function (e) { 
    e.preventDefault();
    let status = $(this).attr('data-status');
    if (status == '200') {
        location.href = './action/cancel';
    }else {
        location.href = './';
    }
    
}); 
$('.cancelOrder').click(function (e) { 
    e.preventDefault();
    location.href = './action/cancel';
});
