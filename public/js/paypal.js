let feedback = $('.feedback')
function initPayPalButton() {
    paypal.Buttons({
        style: {
        shape: 'rect',
        color: 'gold',
        layout: 'vertical',
        label: 'paypal', 
        },

        createOrder: function(data, actions) {
            console.log(data, actions);
            return actions.order.create({
                purchase_units: [{"amount":{"currency_code":"USD","value":parseFloat($('#paypal-button-container').attr('data-price'))}}]
            }); 
        },

        onApprove: function(data, actions) { 
        return actions.order.capture().then(function(details) {
            console.log(details); 
            let id = details.purchase_units[0].payments.captures[0].id;
            feedback.html('<div class="c-green">Transaction completed! Your account will be created in the next hours.</div><br><b>Transaction ID</b><span>' + id + '</span>');
            $('#smart-button-container').hide(); 
            $.ajax({
                method: "POST",
                url: "./action/update",
                data: {status:"Kz443A", id:id},
                dataType: "json",
                success: function (response) {
                  console.log(response);
                  $('.pp-title').html('Transaction Complete') 
                  $('.order-status').html('<div class="c-green">Paid.</div>') 
                  $('.cancelOrder').hide();
                  $('.btn-back').show(); 
                }
              });
        });
        },
        onCancel: function(data,actions) {
            feedback.html('<div class="c-red">Payment cancelled by the user.</div>');
            setTimeout(() => feedback.html(''), 10000);
        },
        onError: function(err) {
            feedback.html(err);
            setTimeout(() => feedback.html(''), 10000);
        }
    }).render('#paypal-button-container');
}
initPayPalButton(); 