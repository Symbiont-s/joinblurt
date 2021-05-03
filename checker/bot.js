var mysql  = require('mysql'); 
var Db     = require('./db.js');
var blurt  = require('@blurtfoundation/blurtjs'); 
var moment = require('moment'); 
// var BlurtHandle  = require('./blurt.js');
const BlurtHandle = require('./blurt.js');
let server = "https://rpc.blurt.world";
blurt.api.setOptions({ url: server, useAppbaseApi: true });
const db    = new Db();
const b     = new BlurtHandle();
var connection; 
let creator;

let open = [];
let close = [];
// main process
function handleConnect() {
    db.data.flags = 'INTERACTIVE';
    connection = mysql.createConnection(db.dbConnection); 
    connection.connect(async function(err) {
        if (err) {
            console.log('Fail to contact with the database' + err); 
        }
        else { 
            console.log('DB connected');
            let data = await getData();
            // updating automatically the information for the db
            setInterval(async () => { 
                data = await getData();
            }, 15000); 
            setInterval(() => {
                getTrx(data);
            }, 30000); 
        }
        setTimeout(() => {
            console.log('connection close');
            connection.end();
        }, 295000);
    }); 
}

async function getTrx(data) {
    console.log('detecting...');
    // let trxlen = await b.getAccountHistory(server,[data.creator[0].name, -1, 1]);
    // let last = JSON.parse(trxlen).result[0][0];
    let trx = await b.getAccountHistory(server,[data.creator[0].name, -1, 100]);
    trx = JSON.parse(trx).result.reverse();
    for (let i = 0; i < trx.length; i++) {
        let o = trx[i][1].op; 
        const txType = o[0];
        const txData = o[1]; 
        if (txType === "transfer") {
            var { amount, from, memo, to } = txData;
            if (to == data.creator[0].name) { 
                    data.open.forEach(order => { 
                        amount = parseFloat(parseFloat(amount).toFixed(3));
                        let received = parseFloat(parseFloat(order.amount).toFixed(3));
                        if (order.memo == memo && amount >= received) {
                        // if (memo == 'TES5656' && amount >= 1) {    
                            console.log('transaction detected');
                            order.from   = from;
                            order.status = 100; 
                            db.updateBlurtOrderStatus(connection, order);
                        }
                    })
            }
        }  
    }
    
}
async function getData() {
    try {
        console.log('enabled');
        open    = [];
        close   = []; 
        creator = await db.getCreatorAccount(connection);
        let orders  = await db.getBlurtOrders(connection, "WHERE status!=100");
        orders.forEach(order => {
            // separing close and open orders
            if (order.status == 0) open.push(order);
            else if(order.status > 0 || order.status < 0) close.push(order); 
        });
        open.forEach(order => {
            let now     = moment();
            let created = moment(order.created);
            let diff    = now.diff(created, 'hours'); 
            if (diff > 3) {
                // auto expiring orders when pass 3 hours
                order.status = -1;
                order.from   = '';
                db.updateBlurtOrderStatus(connection,order);
            }
        });
        close.forEach(order => {
            let now     = moment();
            let created = moment(order.created);
            let diff    = now.diff(created, 'hours');
            //if orders expired pass 24 hours delete, if orders complete delete pass 30 days 
            if (diff > 24 && order.status == -1) db.deleteBlurtOrder(connection,order); 
            else if (diff > 720 && order.status == 200) db.deleteBlurtOrder(connection,order);
        });
        return {
            creator:creator,
            orders:orders,
            open:open,
            close:close
        } 
    } catch (error) {
        console.log(error);
    }
    
}  
handleConnect();