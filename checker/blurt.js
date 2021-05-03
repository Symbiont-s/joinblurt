var blurt   = require('@blurtfoundation/blurtjs'); 
const fetch = require("node-fetch");
blurt.api.setOptions({ url: "https://rpc.blurt.world", useAppbaseApi: true });

class BlurtHandle {
    constructor(){
        this.keys = {};
        this.ops  = [];
        this.fee  = '10.000 BLURT';
        this.gift = '5.000 BLURT';
    }
    generateKeys(username,password,roles = ['owner', 'active', 'posting', 'memo']) { 
        this.keys = blurt.auth.generateKeys(username, password, roles);
        let object = {
            owner : { weight_threshold: 1, account_auths: [], key_auths: [[this.keys.owner, 1]] },
            active : { weight_threshold: 1, account_auths: [], key_auths: [[this.keys.active, 1]] },
            posting : { weight_threshold: 1, account_auths: [], key_auths: [[this.keys.posting, 1]] },
            memo : this.keys.memo
        }
        return object;
    } 
    generateCreateOp(username, password, creator) { 
        let t = this;
        return new Promise((resolve,reject) => {
            blurt.api.getChainProperties(function (err, result) {
                if (!err){
                    t.fee = result.account_creation_fee;
                    let keys = t.generateKeys(username,password);
                    let owner = keys.owner;
                    let posting = keys.posting;
                    let op = [
                        'account_create',
                        {
                            active: keys.active,
                            creator,
                            extensions: [],
                            fee: t.fee,
                            json_metadata: '',
                            memo_key: keys.memo,
                            new_account_name: username,
                            owner,
                            posting,
                        },
                    ];
                    resolve(op);
                }
                else reject(err); 
            }); 
        }).then(r => {return r})
          .catch(e => { console.log(e); return false; }); 
    }
    async createAccount(username, password, creator) { 
        let d = await this.generateCreateOp(username, password, creator.name); 
        this.ops.push(d);
        const gift_op = ['transfer', {
            from: creator.name,
            to: username,
            amount: this.gift,
            memo: 'Welcome to BLURT!'
        }];
        this.ops.push(gift_op);
        return new Promise((resolve, reject) => {
            blurt.broadcast.send(
                { operations: this.ops, extensions: [] },
                { active: creator.key },
                function (err, result){
                    if (err) {
                        console.log(err);
                        resolve(false);
                    }else {
                        console.log(result);
                        resolve(true);
                    }
                }
            );
        });
    }
    async getAccountHistory(server, object) {
        return new Promise((resolve, reject) => {
            fetch(server, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({"jsonrpc":"2.0", 
                                    "method":"condenser_api.get_account_history",
                                    "params":object, 
                                    "id":1})
            }).then(response => { resolve(response.text());})
              .catch(error => { reject(error); });
        });
    }
}
module.exports = BlurtHandle;