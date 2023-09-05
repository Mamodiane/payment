<?php
require_once "config.php";

$payment_id = $_SESSION['payment_id'];

// check if transaction exists
$db = new DB;
$transaction = $db->get_transaction($payment_id);

if ('completed' == $transaction['status']) {
    echo "Payment is successful. Your Payment ID is $transaction";
} elseif ('failed' == $transaction['status']) {
    echo "Payment is failed.";
}