var mysql  = require('mysql');
var Db     = require('../db.js');
var moment = require('moment');  
let db    = new Db();
var connection  = mysql.createConnection(db.dbConnection);
connection.connect(async function(err) {
    if (err) console.log('Fail to contact with the database' + err);
    else {
        console.log('DB connected');
        let creator = await db.getCreatorAccount(connection);
        let orders = await db.getBlurtOrders(connection);
        orders.forEach(order => {
            let now = moment();
            let created = moment(order.created);
            let diff = now.diff(created, 'hours');
            console.log(diff);
            if (diff > 3) {
                order.status = -1;
                order.from   = '';
                db.updateBlurtOrderStatus(connection,order);
            }
        })
    }
});