<?php
require_once "config.php";
$preparedData = $this->payment_model->razorpayPrepareData($payment_gateway['identifier']);

// Start a new session
//session_start();
$payment_details = $preparedData['amount'];
$db = new DB;

function generateSignature($data, $passPhrase = null){
    // Create parameter string
    $pfOutput = '';
    foreach ($data as $key => $val) {
        if ($val !== '') {
            $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
        }
    }
    // Remove last ampersand
    $getString = substr($pfOutput, 0, -1);
    if ($passPhrase !== null) {
        $getString .= '&passphrase=' . urlencode(trim($passPhrase));
    }
    return md5($getString);
}
// generate unique payment id

$_SESSION['payment_id'] = time() . uniqid();
$payment_id = $_SESSION['payment_id'];
// Set the amount from your session variable
$payable_amount =  $payment_details;
// insert initial transaction
$arr_data = array(
    'payment_id' => $payment_id,
    'amount' => $payable_amount,
);

$db = new DB;
$db->upsert_transaction($arr_data);
$data = array(
    // Merchant details
    'merchant_id' => PAYFAST_MERCHANT_ID,
    'merchant_key' => PAYFAST_MERCHANT_KEY,
    'return_url' => PAYFAST_RETURN_URL,
    'cancel_url' => PAYFAST_CANCEL_URL,
    'notify_url' => PAYFAST_NOTIFY_URL,
    // Transaction details
    'm_payment_id' => $payment_id,
    'amount' => number_format($payable_amount, 2, '.', ''), // Format the amount
    'item_name' => 'Your Product Name',
);

$signature = generateSignature($data, PAYFAST_PASSPHRASE);
$data['signature'] = $signature;
$pfHost = PAYFAST_SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
$htmlForm = '<form action="https://' . $pfHost . '/eng/process" method="post" id="frmPayment">';
foreach ($data as $name => $value) {
    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
}
$htmlForm .= '<input type="submit" value="Pay Now" style="display:none;" /></form>';
echo $htmlForm;
?>
<h3>Redirecting to PayFast...</h3>
<script>
    window.addEventListener('load', (event) => {
        document.getElementById("frmPayment").submit();
    });
</script>
