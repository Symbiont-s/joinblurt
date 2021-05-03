var nodemailer   = require('nodemailer');
var Mail         = require('../mail.js'); 
let mail         = new Mail();

const MAIL_TEST = "demarzomonterojose@gmail.com";

var transporter = nodemailer.createTransport(mail.connection); 
//change it to your mail to test
mail.setReminder(MAIL_TEST);

mail.setBody(mail.buildBodyMessage("test", "ytredfghgtrtygfv"));
transporter.sendMail(mail.options, function(error, info){
    if (error) {
      console.log(error);
    } else {
        console.log('Email sended successfully!');
    }
});