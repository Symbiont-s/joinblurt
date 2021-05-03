### JoinBlurt

This project aims to provide a tool that will allow POs to offer diverse payment methods to create accounts on the Blurt Blockchain.

The payment methods that are currently supported are:

* BLURT, which allows people to create accounts by using their existing accounts; 
* Crypto, which allows people to pay with crypto to create accounts; 
* and finally, debit/credit Card and PayPal.

### Requeriments

* PHP 7.0 or above;
* Cronjob;
* Nodejs 10.19.0 or above;
* Node-mysql;
* Blurtjs;
* Nodemailer;
* Coinpayments dependencies;
* PayPal dependencies;
* and pm2 node package manager.

### First Step

0- Install phpMyAdmin.

1- Import the SQL file on phpMyAdmin.
    
2- Go to the CoinPayments table and add your secret key, public key, and Username (not mandatory). The keys can be generated on:
https://coinpayments.net/index.php?cmd=acct_api_keys

3- Go to the PayPal table and add your PayPal email (mandatory), your client ID, and your secret. All is provided by PayPal on:
https://developer.paypal.com.

4- Go to the settings table and add the Blurt account that you will be using to created accounts, and one administrator email (not mandatory, will be used to get notifications in case the account creation fails). 

5- Go to ```controller/config.php``` and set your database connection.

6- Go to ```checker/db.js``` to set your database connection. This is for the cronjob that will check orders status.    
   
7- Edit the ```this.data``` property for DB class.

8- Go to ```checker/mail.js``` to set your email account.

Edit the following lines: (privateemail.com / the syntax below can be different for other provider such as Gmail)
   
   ```
  class Mail {
    constructor(){
        this.connection = {
            "host":"mail.privateemail.com",
            "port":465,
            "secure":true,
            auth: {
                type:"login",
                user: 'support@joinblurt.com',
                pass: ''
            }
        } 
   ```

9- Go to ```public/templates/libraries.php``` and edit the ```<base>``` HTML tag, you must add your hostname.

   ```<base href="https://yourdomain.com/">```

### Checker Installation

After cloning this repo, move to the checker folder and install the dependencies.

```cd checker```

```npm i```

### Cronjobs Installation

On your bash execute:

```crontab -e```

To edit the crontab file, and then add your cronjobs. The project/tool use one cronjob to check every minute the status of the open orders, do:

```*/1 * * * * node /var/www/html/checker/index.js```

Then save.

### BOT Installation

We use a node bot to detect when a Blurt transaction is received and save it in the database.

Start the bot:

```pm2 start /var/www/html/checker/bot.js```

As NodeJS is somewhat unreliable, it's recommended to use a cron to auto restart the bot. The following line restarts your bot every 5 minutes, you can set the time what you want:

``` pm2 start bot --cron "*/5 * * * *"```

### Testing The Mail Service

You are almost ready to use your checker! You just need to go to ```checker/test/mail.js``` and add a testing mail (can be your personal email), to ```MAIL_TEST``` constant.

```
const MAIL_TEST = 'yourmail@mail.com'
```

Then execute:

```node checker/test/mail.js```

If your email service is working you will receive a testing email to the address that you set above. (Check spam if needed)

### Testing the DB Connection

You must execute:

```node checker/test/db.js```

If your db connection is working you will see a ```connected``` message on your bash.

### Additional Notes

If you have disabled the use of .htaccess files on your dedicated host please follow these steps.

* To enable htaccess (e.g Apache) on your host You must edit apache2.conf file at ```/etc/apache2/```.

Find:

```
<Directory /var/www/>
	Options Indexes FollowSymLinks
	AllowOverride None
	Require all granted
</Directory>
```

and change 'AllowOverride' to 'All'.

* Next step will be to enable rewrite module executing

```sudo a2enmod rewrite```

To apply changes do:

```sudo service apache2 restart``` 

* Change the permissions for the checker folder to avoid a data leak, do:

```chmod 770 /var/www/html/checker/```

### License

GNU GENERAL PUBLIC LICENSE Version 3.

Brought to you by the Symbionts Team.
