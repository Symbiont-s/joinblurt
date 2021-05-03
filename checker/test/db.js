var mysql = require('mysql');
var Db    = require('../db.js');
let db = new Db();

var connection  = mysql.createConnection(db.dbConnection);
connection.connect(function(err){
    if (err) {
        console.log(err);
    }else{
        console.log('connected');
    }
})