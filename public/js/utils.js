let defaultServer = "https://rpc.blurt.world";
const DEFAULT_CREATION_FEE = '10.000 BLURT';
blurt.api.setOptions({ url: defaultServer, useAppbaseApi: true });
const regx = {
  username:/^[a-z](-[a-z0-9](-[a-z0-9])*)?(-[a-z0-9]|[a-z0-9])*(?:\.[a-z](-[a-z0-9](-[a-z0-9])*)?(-[a-z0-9]|[a-z0-9])*)*$/,
  password:/^([0-9a-zA-Z]+)$/,
  number:/^([0-9]+)$/
}
let a = ($(".pm-method").attr('data-id'))?$(".pm-method").attr('data-id'):'order';
let method = (a).toLowerCase(); 
const checkAccountName = async (username) => {
    if (username == '') return false;
    const [ac] = await blurt.api.getAccountsAsync([username]);
    return (ac === undefined) ? true : false;
},
generateKeys = (username, password, roles = ['owner', 'active', 'posting', 'memo']) => {
    const privKeys = {};
    roles.forEach((role) => {
      privKeys[role] = dsteem.PrivateKey.fromLogin(username, password, role).toString();
      let selector = '#' + role + '-key';
      $(selector).val(privKeys[role]);
      privKeys[`${role}Pubkey`] = dsteem.PrivateKey.from(privKeys[role]).createPublic().toString();
    });
  
    return privKeys;
},
buildFinalMessage = (username) => {
  return `<div class="container"><div class="row"><div class="col-12 offset-0 col-sm-10 offset-sm-1 col-md-6 offset-md-3" style="margin-top:35px;">
            <div class="creator-content ta-c">
            <h2>Your account has been created successfully.</h2><br>
            <p>View on <a href="https://ecosynthesizer.com/blurt/@${username}">Ecosynthesizer.com</a> | <a href="https://blurt.blog/@${username}">Blurt.blog</a></p> 
          </div></div></div></div>`;
},
suggestPassword = () => {
    const array = new Uint32Array(10);
    window.crypto.getRandomValues(array);
    return 'P' + dsteem.PrivateKey.fromSeed(array).toString();
},
getAccountCreationFee = async () => {
    return new Promise(function (resolve) {
      blurt.api.getChainProperties(function (err, result) {
        if (!err && result) {
          resolve(result.account_creation_fee);
        } else {
          resolve(DEFAULT_CREATION_FEE);
        }
      });
    });
},
getAccountPrice = async () => {
  return new Promise(function (resolve) {
    $.ajax({
      method: "GET",
      url: "./action/price", 
      success: function (response) { 
        resolve(response);
      }
    });
  });
}
getCurrencyPrice = async (currency, value = "usd") => { 
  return new Promise((resolve, reject) => {
      fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${currency}&vs_currencies=${value}`, {
      method: "GET"
      }).then(response => { resolve(response.text());})
        .catch(error => { reject(error); });
  });
}