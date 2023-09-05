<?php
class DB {
    private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "fbkonline";
 
    public function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
                $this->db = $conn;
            }
        }
    }

    public function get_transaction($payment_id) {
        $sql = $this->db->query("SELECT * FROM transactions WHERE payment_id = '$payment_id'");
        return $sql->fetch_assoc();
    }
 
    public function upsert_transaction($arr_data = array()) {
        $payment_id = $arr_data['payment_id'];
 
        // check if transaction exists
        $transaction = $this->get_transaction($payment_id);
 
        if(!$transaction) {
            // insert the transaction
            //$product_id = $arr_data['product_id'];
           // $first_name = $arr_data['first_name'];
           // $last_name = $arr_data['last_name'];
           // $email = $arr_data['email'];
            $amount = $arr_data['amount'];
            $this->db->query("INSERT INTO transactions(payment_id, product_id, first_name, last_name, email, amount) VALUES('$payment_id', '$product_id', '$first_name', '$last_name', '$email', '$amount')");
        } else {
            // update the transaction
            $status = $arr_data['status'];
            $this->db->query("UPDATE transactions SET status = '$status' WHERE payment_id = '$payment_id'");
        }
    }
}