var Coinpayments = require('coinpayments');
var nodemailer   = require('nodemailer');
var mysql        = require('mysql');
var Mail         = require('./mail.js');
var Db           = require('./db.js');
var BlurtHandle  = require('./blurt.js');
let mail         = new Mail();
let db           = new Db();
const transporter= nodemailer.createTransport(mail.connection); 
var connection   = mysql.createConnection(db.dbConnection);
var blurt        = new BlurtHandle(); 
let keys;
let creator;
let orders;
connection.connect(async function (err) {
    if (err) console.log('Fail to contact with the database' + err);
    else {
        //connecting to db
        console.log('connected'); 
        keys = await db.getKeys(connection); // coinpayment keys
        creator = await db.getCreatorAccount(connection); //creator account
        orders = await db.getOrders(connection); // get orders
        blurtOrders = await db.getBlurtOrders(connection,'WHERE status=100');
        // creating new Coinpayments client
        const client = new Coinpayments({key:keys[0].public, secret:keys[0].private}); 
        let pendingOrders = [];
        let failed = false; 
        orders.forEach(async (order) => { 
            // if orders is pending o complete but no arrived, check
            if (order.method == 'coinpayments') {
                if (order.status >= 0 && order.status != 200) {
                    let info = await client.getTx({txid:order.tx_id});
                    if (info.status != order.status) {let response = await db.updateOrderStatus(connection, {status:info.status, id:order.id});}
                    if (info.status == 100 || info.status == 2) {// if order is complete create account 
                        let wasCreated = await blurt.createAccount(order.metadata.username, order.metadata.master_key, creator[0]);
                        if (wasCreated) {
                            console.log('Account created successfully');
                            let response = await db.updateOrderStatus(connection, {status:200, id:order.id});
                            mail.setReminder(order.buyer_email);
                            mail.setBody(mail.buildBodyMessage(order.metadata.username, order.metadata.master_key));
                            transporter.sendMail(mail.options, function(error, info){
                                if (error) {
                                console.log(error);
                                } 
                            });
                        }else { 
                            // if some fail create a list
                            pendingOrders.push(order);
                            failed = true; 
                        }
                    }
                }else if (order.status < 0 || order.status == 200){
                    // when the order expired or complete, move to close
                    let res = await db.moveOrder(connection, order);
                }
            }else if(order.method == 'paypal') {
                // Processing paypal orders
                if (order.status == 100) {
                    let wasCreated = await blurt.createAccount(order.metadata.username, order.metadata.master_key, creator[0]);
                    if (wasCreated) {
                        console.log('Account created successfully');
                        let response = await db.updateOrderStatus(connection, {status:200, id:order.id});
                        mail.setReminder(order.buyer_email);
                        mail.setBody(mail.buildBodyMessage(order.metadata.username,order.metadata.master_key));
                        transporter.sendMail(mail.options, function(error, info){
                            if (error) console.log(error); 
                        });
                    }
                }else if(order.status == -1 || order.status == 200) {
                    let res = await db.moveOrder(connection, order);
                }
            } 
        });
        if (blurtOrders.length > 0) {
            t = nodemailer.createTransport(mail.connection);
            blurtOrders.forEach(async (order) => { 
                let wasCreated = await blurt.createAccount(order.metadata.username, order.metadata.master_key, creator[0]);
                if (wasCreated) {
                    console.log('Account created successfully');
                    order.status = 200; 
                    let response = await db.updateBlurtOrderStatus(connection, order);
                    mail.setReminder(order.buyer_email);
                    mail.setBody(mail.buildBodyMessage(order.metadata.username,order.metadata.master_key));
                    t.sendMail(mail.options, function(error, info){
                        if (error) console.log(error); 
                    });
                }
                //testing
                    // order.status = 200; 
                    // let response = await db.updateBlurtOrderStatus(connection, order);
                    // mail.setReminder("demarzomonterojose@gmail.com");
                    // mail.setBody(mail.buildBodyMessage(order.metadata.username, order.metadata.master_key));
                    // t.sendMail(mail.options, function(error, info){
                    //     if (error) console.log(error);
                    //     else console.log(info);
                    // });
            });
        }
        setTimeout(() => {
            console.log('connection close');
            connection.end();
        }, 59000);
        
    }
});



