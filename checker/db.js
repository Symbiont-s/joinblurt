class Db {
    constructor() {
        this.data = {
            host : 'localhost',  
            database : 'ecs_account_creator',
            user : 'root',
            password : ''
        }
        this.dbConnection = this.getDataConnection();
    }
    getDataConnection(){
        return this.data;
    }
    getKeys(connection){
        return new Promise((resolve,reject) =>{
            connection.query('SELECT * FROM coinpayments', function(err, results){
                if (err)  reject(err);
                else{
                    let keys = [];
                    results.forEach((r) => { 
                        keys.push({
                            public:r.public,
                            private:r.private
                        });
                    });
                    resolve(keys);
                }
            })
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    getCreatorAccount(connection){
        return new Promise((resolve,reject) =>{
            connection.query('SELECT * FROM settings', function(err, results){
                if (err)  reject(err);
                else{
                    let creator = [];
                    results.forEach((r) => { 
                        creator.push({
                            name:r.creator,
                            key:r.creatorKey,
                            mail:r.admin_mail
                        });
                    });
                    resolve(creator);
                }
            })
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    getOrders(connection){
        return new Promise((resolve,reject) =>{
            connection.query('SELECT * FROM open_orders', function(err, results){
                if (err)  reject(err);
                else{
                    let orders = [];
                    results.forEach((r) => { 
                        orders.push({
                            id:r.id,
                            tx_id:r.tx_id,
                            amount:r.amount,
                            currency:r.currency,
                            buyer_email:r.buyer_email,
                            metadata:JSON.parse(r.account_metadata),
                            method:r.method,
                            status:r.status, 
                            time:r.time
                        });
                    });
                    resolve(orders);
                }
            })
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    getBlurtOrders(connection, condition = ''){
        return new Promise((resolve,reject) =>{
            connection.query(`SELECT * FROM blurt_orders ${condition}`, function(err, results){
                if (err)  reject(err);
                else{
                    let orders = [];
                    results.forEach((r) => { 
                        orders.push({
                            id:r.id,
                            memo:r.memo, 
                            buyer_email:r.buyer_email,
                            metadata:JSON.parse(r.account_metadata), 
                            amount:r.amount,
                            from:r.user,
                            status:r.status, 
                            created:r.created
                        });
                    });
                    resolve(orders);
                }
            })
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    moveOrder(connection, order) {
        return new Promise((resolve,reject) =>{
            let meta = JSON.stringify(order.metadata); 
            connection.query(`INSERT INTO close_orders (account_metadata, buyer_email, tx_id,method, status) VALUES ('${meta}','${order.buyer_email}','${order.tx_id}','${order.method}', ${order.status})`, function (err, f) {
                if (err) {
                    reject(err);
                }else {
                    connection.query(`DELETE FROM open_orders WHERE id = ${order.id}`, function (err, r){
                        if (err) {
                            reject(err);
                        }else{
                            resolve(true);
                        }
                    })
                }
            });
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    updateOrderStatus(connection, data) {
        return new Promise((resolve,reject) =>{
            connection.query(`UPDATE open_orders SET status=${data.status} WHERE id=${data.id}`, function (err, results) {
                if (err) {
                    reject(err);
                }else {
                    resolve(true);
                }
            });
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    updateBlurtOrderStatus(connection, order) {
        return new Promise((resolve,reject) =>{
            connection.query(`UPDATE blurt_orders SET status=${order.status}, user='${order.from}' WHERE id=${order.id}`, function (err, results) {
                if (err) {
                    reject(err);
                }else {
                    resolve(true);
                }
            });
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
    deleteBlurtOrder(connection, order) {
        return new Promise((resolve,reject) =>{
            connection.query(`DELETE FROM blurt_orders WHERE id=${order.id}`, function (err, results) {
                if (err) {
                    reject(err);
                }else {
                    resolve(true);
                }
            });
        }).then(r => { return r; })
          .catch(e => {console.log('Fail to get info. ' + e); return false;});
    }
}
module.exports = Db;