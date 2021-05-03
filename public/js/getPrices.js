(async () => {
    let fee   = await getAccountCreationFee();
    let price = await getAccountPrice();

    $('.prices').html(`$${JSON.parse(price).price} (Account + Starting Package)`);
    $('.fee').html(fee);
})();