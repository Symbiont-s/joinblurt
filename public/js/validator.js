const requiredMessage = "Fill the field.";
let accname = false;
let accpasword = true;
const validate = {
    username: (selectors) => {
        selectors.input.keyup(function() {
            let t = $(this); 
            let username = t.val();
            if (username.length >= 3 && username.match(regx.username) && username.length < 16) {
                t.addClass('is-valid')
                 .removeClass('is-invalid');
                selectors.label.addClass('d-none')
                               .html('');
                accname = true; 
            }else {
                t.removeClass('is-valid')
                 .addClass('is-invalid');
                selectors.label.removeClass('d-none')
                               .html(requiredMessage);
                accname = false; 
            }
        });
    },
    password: (selectors) => {
        selectors.input.keyup(function (e) { 
            let t = $(this); 
            let password = t.val();
            if (password.length >= 8 && password.match(regx.password)) {
                t.addClass('is-valid')
                 .removeClass('is-invalid');
                selectors.label.addClass('d-none')
                         .html('');
                accpasword = true;
            }else {
                t.removeClass('is-valid')
                 .addClass('is-invalid');
                selectors.label.removeClass('d-none')
                               .html(requiredMessage)
                accpasword = false; 
            }
        });
    }
}
validate.username({input:$("#username"), label:$('.username-error')});
validate.password({input:$("#key"), label:$('.key-error')});
$("#username").keyup(() => {
    $('.userIsAvailable').removeClass('d-none');
    $(".availability").addClass("d-none");
})

switch (method) {
    case 'blurt': 
        validate.username({input:$("#creator"), label:$('.creator-error')});
        validate.password({input:$("#c-key"), label:$('.c-key-error')});
        break;
    case 'card':

        break;
    case 'crypto':
        $('#payment-with-crypto').validate({
            rules:{
                currency:'required',
                price:'required',
                email:{required:true, email:true}
            },
            messages:{
                currency:requiredMessage,
                price:requiredMessage,
                email:{required:requiredMessage, email:"Please just example@example.com"}
            }
        });
        $('#payment-with-crypto').submit(() => {
            console.log('submitting...');
            if (!accname) {
                $('.username-error').removeClass('d-none')
                                    .html(requiredMessage);
                return false;
            }
            if (!accpasword) {
                $('.key-error').removeClass('d-none')
                               .html(requiredMessage);
                return false;
            }
        })
        break;   
    default:
        break;
}

