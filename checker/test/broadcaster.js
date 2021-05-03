var blurt  = require('@blurtfoundation/blurtjs'); 
blurt.api.setOptions({ url: "https://rpc.blurt.world", useAppbaseApi: true }); 

blurt.api.streamTransactions('head', (err,result) => {
    console.log(result.block_num);
    const txType = operations[0];
    const txData = operations[1];
});