<?php
/**
 * CLASSES FOR ECS ACCOUNT CREATOR
 * Included:
 * Handler             - class to start a db connection
 * Order               - class to init new order and do operations
 * Paypalhandler       - class to connect paypal operations with the db
 * CoinpaymentsHandler - class to connect coinpayments operations with the db
 * 
 * EXTRA:
 * getStatusMessage function - used to codificate the status code to text
 */
    require_once('coinpayments-php/src/CoinpaymentsAPI.php');
    class Handler {
        protected $connection;
        public function __construct($connection){
			$this->setConnection($connection);
        }
		public function setConnection(PDO $connection){
			$this->connection=$connection;
        }
        public function getSettings(){
            $sql = "SELECT * FROM settings";
            $result = $this->connection->query($sql);
            while($response=$result->fetch(PDO::FETCH_ASSOC)){
                $settings = array(
                    "creator"  => $response['creator'],
                    "account_price" => $response['account_price']
                );
            }
            return $settings;
        } 
    }
    class Order {
        private $id;
        private $tx_id;
        private $amount;
        private $currency;
        private $buyer_email;
        private $deposit_address;
        private $coinpaymentUrl;
        private $metadata;
        private $method;
        private $status;
        private $timeout = 1800;
        private $qr;
        public function setId($id){
            $this->id = $id;
        }
        public function getId(){
            return $this->id;
        }
        public function setTxId($tx_id){
            $this->tx_id = $tx_id;
        }
        public function getTxId(){
            return $this->tx_id;
        }
        public function setAmount($amount){
            $this->amount = $amount;
        }
        public function getAmount(){
            return $this->amount;
        }
        public function setCurrency($currency){
            $this->currency = $currency;
        }
        public function getCurrency(){
            return $this->currency;
        }
        public function setBuyerEmail($buyer_email){
            $this->buyer_email = $buyer_email;
        }
        public function getBuyerEmail(){
            return $this->buyer_email;
        }
        public function setDepositAddress($deposit_address){
            $this->deposit_address = $deposit_address;
        }
        public function getDepositAddress(){
            return $this->deposit_address;
        }
        public function setUrl($coinpaymentUrl){
            $this->coinpaymentUrl = $coinpaymentUrl;
        }
        public function getUrl(){
            return $this->coinpaymentUrl;
        } 
        public function setMetadata($metadata){
            $this->metadata = $metadata;
        }
        public function getMetadata(){
            return $this->metadata;
        }
        public function setMethod($method){
            $this->method = $method;
        }
        public function getMethod(){
            return $this->method;
        }
        public function setStatus($status){
            $this->status = $status;
        }
        public function getStatus(){
            return $this->status;
        }
        public function setTimeOut($timeout){
            $this->timeout = $timeout;
        }
        public function getTimeout(){
            return $this->timeout;
        }
        public function setQr($qr){
            $this->qr = $qr;
        }
        public function getQr(){
            return $this->qr;
        }
    }
    class BlurtHandler extends Handler {
        public function createOrder(Order $order, $account_metadata){
            $sql = "INSERT INTO blurt_orders (memo, account_metadata,buyer_email,amount, status, created) VALUES 
                   ('" . $order->getDepositAddress() . "',
                   '" . $account_metadata . "',
                   '" . $order->getBuyerEmail() . "',
                   " . $order->getAmount() . ",
                   " . $order->getStatus() . ",
                   now())";
            $this->connection->query($sql);
        }
        public function orderExist($id, $field=false) {
            $sql = "SELECT * FROM blurt_orders ";
            $sql .= ($field)? "WHERE " . $field . "='" . $id . "'": "WHERE id=" . $id;
            $result = $this->connection->query($sql);
            while($response=$result->fetch(PDO::FETCH_ASSOC)){
                $order = array(
                    "id"  => $response['id'],
                    "memo" => $response['memo'],
                    "metadata" => $response['account_metadata'],
                    "amount" => $response['amount'],
                    "status" => $response['status']
                );
            }
            return $order;
        } 
    }
    class PaypalHandler extends Handler {
        public function getClientID() {
            $sql = 'SELECT * FROM paypal';
            $result = $this->connection->query($sql);
            while($response=$result->fetch(PDO::FETCH_ASSOC)){
                $data = array(
                    "client"  => $response['client_id'],
                    "secret" => $response['secret']
                );
            }
            return $data;
        }
        public function createOrder(Order $order, $account_metadata){
            $sql = "INSERT INTO open_orders (tx_id,amount,currency,buyer_email,account_metadata,method,time) VALUES (
                    '" . $order->getTxId() . "',
                    '" . $order->getAmount() . "',
                    '" . $order->getCurrency() . "',
                    '" . $order->getBuyerEmail() . "',
                    '" . $account_metadata . "',
                    '" . $order->getMethod() . "',
                    now())";
            $this->connection->query($sql); 
            $exist = $this->orderExist($order->getTxId());
            if (!$exist) { return false; }
            else {
                $order->setId($exist['id']);
                return $order;
            }
        }
        public function orderExist($id) {
            $sql    = "SELECT * FROM open_orders WHERE tx_id = '" . $id . "'";
            $sql2   = "SELECT * FROM close_orders WHERE tx_id = '" . $id . "'";
            $result = $this->connection->query($sql);
            $order  = false;
            while($response=$result->fetch(PDO::FETCH_ASSOC)){
                $order = array(
                    "id"  => $response['id'],
                    "tx_id" => $response['tx_id'],
                    "amount" => $response['amount'],
                    "currency" => $response['currency'],
                    "buyer_email" => $response['buyer_email'],
                    "metadata" => $response['account_metadata'],
                    "method" => $response['method'],
                    "status" => $response['status']
                );
            }
            if (!$order) {
                $result = $this->connection->query($sql2);
                while($response=$result->fetch(PDO::FETCH_ASSOC)){
                    $order = array(
                        "id"  => $response['id'],
                        "tx_id" => $response['tx_id'],
                        "buyer_email" => $response['buyer_email'],
                        "metadata" => $response['account_metadata'],
                        "method" => $response['method'],
                        "status" => $response['status']
                    );
                }
            }
            return $order;
        }
        public function updateOrder(Order $order, $txid) {
            $sql = "UPDATE open_orders SET status =" . $order->getStatus() . ", tx_id='" . $order->getTxId() . "' WHERE tx_id='" . $txid . "'";
            $this->connection->query($sql);
            return true;
        }
    }
    class CoinpaymentsHandler extends Handler {
        public function getKeys(){
            $sql    = 'SELECT * FROM coinpayments';
            $result = $this->connection->query($sql);
            while($response=$result->fetch(PDO::FETCH_ASSOC)){
                $keys = array(
                    "public"  => $response['public'],
                    "private" => $response['private']
                );
            }
            return $keys;
        }
        public function getAcceptedCurrencies(){
            $keys = $this->getKeys();
            $cps_api = new CoinpaymentsAPI($keys['private'], $keys['public'], 'json');
            try {
                $rates = $cps_api->GetRatesWithAccepted();
            } catch (Exception $e) {
                return false;
            }

            if ($rates["error"] == "ok") {
                //building currencies list
                $list = [];
                $dollar = $rates['result']['USD']['rate_btc'];
                foreach ($rates['result'] as $rate => $rate_array) {
                    if ($rate_array['accepted']) {
                        $o = array(
                            "currency" => $rate,
                            "rate"     => ($rate == 'BTC')?$dollar : $dollar / $rate_array['rate_btc']
                        );
                        array_push($list,$o);
                    }
                }
                if (count($list) == 0) {
                    $list = ['LTC'];
                }
                return $list;
            } else {
                return false;
            }
        }
        public function createOrder(Order $order, $account_metadata){
            // creating coinpayments instance
            $keys = $this->getKeys();
            $cps_api = new CoinpaymentsAPI($keys['private'], $keys['public'], 'json');
            //creating new order 
            try {
                $response = $cps_api->CreateSimpleTransaction($order->getAmount(), $order->getCurrency(), $order->getBuyerEmail());
            } catch (Exception $e) {
                return false;
            }
            if ($response['error'] == 'ok') {
                # order create successfully
                $order->setTxId($response['result']['txn_id']);
                $order->setDepositAddress($response['result']['address']);
                
                $order->setStatus(0);
                $newOrder = $this->saveOrder($order, $account_metadata);
                $newOrder->setUrl($response['result']['status_url']);
                $newOrder->setTimeOut($response['result']['timeout']);
                $newOrder->setQr($response['result']['qrcode_url']);
                return $newOrder;
            }else{
                return false;
            }
        }
        public function saveOrder(Order $order, $account_metadata){
            $sql = "INSERT INTO open_orders (tx_id,amount,currency,buyer_email,deposit_address,account_metadata,status, time) VALUES 
                    ('" . $order->getTxId() . "',
                    " . $order->getAmount() . ",
                    '" . $order->getCurrency() . "',
                    '" . $order->getBuyerEmail() . "',
                    '" . $order->getDepositAddress() . "',
                    '" . $account_metadata . "',
                    '" . $order->getStatus() . "',
                    now())";
            $this->connection->query($sql);
            $response = $this->orderExist($order->getTxId());
            return $response; 
        }
        public function orderExist($id) {
            $sql1    = "SELECT * FROM open_orders WHERE tx_id='" . $id . "'";
            $sql2    = "SELECT * FROM close_orders WHERE tx_id='" . $id . "'";
            $data    = new Order();
            $result1 = $this->connection->query($sql1);
            $result2 = $this->connection->query($sql2);
            while($response=$result1->fetch(PDO::FETCH_ASSOC)) {
                if ($response['tx_id'] == $id) {
                    # checking if order was created
                    $data->setId($response['id']);
                    $data->setTxId($response['tx_id']);
                    $data->setBuyerEmail($response['buyer_email']);
                    $data->setAmount($response['amount']);
                    $data->setCurrency($response['currency']);
                    $data->setDepositAddress($response['deposit_address']);
                    $data->setMetadata($response['account_metadata']);
                    $data->setStatus($response['status']);
                }
            }
            while($response=$result2->fetch(PDO::FETCH_ASSOC)) {
                if ($response['tx_id'] == $id) {
                    # checking if order was created
                    $data->setId($response['id']);
                    $data->setTxId($response['tx_id']);
                    $data->setBuyerEmail($response['buyer_email']); 
                    $data->setStatus($response['status']);
                }
            }
            if (!empty($data->getTxId())) {
                # order exist
                return $data;
            }else { 
                return false;
            }
        }
        public function updateStatus($id, $status){
            $sql = "UPDATE open_orders SET status=" . $status . " WHERE tx_id='" . $id . "'";
            $this->connection->query($sql);
        }
    }
    function getStatusMessage($code) {
        switch ($code) {
            case -2:
                return 'Refund';
                break;
            case -1:
                return '<div class="c-red">Cancelled / Time Out</div>';
                break;
            case 0:
                return 'Pending';
                break;
            case 1:
                return '<div class="c-green">Transaction detected. Waiting for confirmation.</div>';
                break;
            case 100:
                return '<div class="c-green">Transaction Completed. You can leave this page now.</div>';
                break;
            case 200:
                return '<div class="c-blue">Account Created.</div>';
                break;
            default:
                return 'Processing';
                break;
        }
    }
?>