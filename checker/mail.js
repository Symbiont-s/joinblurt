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
        this.options = {
            from: this.connection.auth.user,
            to: '',
            subject: 'Symbionts | Account Creation Successful',
            html: ''
        }
    }
    setReminder(reminder) {
        this.options.to = reminder;
    }
    setBody(body){
        this.options.html = body;
    }
    setTitle(title){
        this.options.subject = title;
    }
    getMailConnection() {
        return this.connection;
    }
    getMailOptions() {
        return this.options;
    }
    buildBodyMessage(username, key){
        return `Welcome to Blurt! You can start using your account now.<br><br>
        Go to <a href='https://blurtwallet.com'>Blurt Wallet</a> if you want to check your wallet, or send and receive assets.<br><br>
        If you want to see more details about your account, you can visit <a href="https://ecosynthesizer.com/blurt/">Ecosynthesizer</a>.<br><br>
        Account Name: ${username}<br><br>
        Master Password: ${key}<br><br>
        Additional notes:<br><br>
        0- Make sure to change your keys as soon as possible. Trust no one, us included.<br>
        1- You should never use your master key online unless you want to change your keys.<br>
        2- To post and vote on Blurt, use your posting key.<br>
        3- To send assets, use your active key.<br>
        4- If you want to change your keys, go to the wallet and login with your master key.<br><br>
        Thank you for using our service,<br><br>
        The Symbionts Team,`; 
    }
}
module.exports = Mail