<?php
/*This file generates the payment request and sends it to the Sips server
For more information on this use case, please refer to the following documentation:
https://documentation.sips.worldline.com/en/WLSIPS.004-GD-Functionality-set-up-guide.html#Payment-in-instalments */

session_start();

include('Common/sealCalculationPaypagePost.php');
include('Common/flatten.php');
include('Common/transactionIdCalculation.php');


//PAYMENT REQUEST

// parameters.php initializes some session data like $_SESSION['merchantId'], $_SESSION['secretKey'], $_SESSION['normalReturnUrl'] and $_SESSION["urlForPaymentInitialisation"]
// You can change these values in parameters.php according to your needs and architecture
include('parameters.php');

// Merchants migrating from WL Sips 1.0 to WL Sips 2.0 must provide a transactionId. This is easily done below. (second example used as default).

// Example with the merchant's own transactionId (typically when you increment Ids from your database)
// $s10TransactionReference=array(
//    "s10TransactionId" => "000001",
// //   "s10TransactionIdDate" => "not needed",   Please note that the date is not needed, Sips server will apply its date.
// );
//
// Example with transactionId automatic generation, like the WL Sips 1.0 API was doing.
$s10TransactionReference1=get_s10TransactionReference();
$s10TransactionId1=$s10TransactionReference1["s10TransactionId"];
$s10TransactionId2=(string)($s10TransactionId1+1);
$s10TransactionId3=(string)($s10TransactionId1+2);


$requestData = array(
   "normalReturnUrl" => $_SESSION['normalReturnUrl'],
   "merchantId" => $_SESSION['merchantId'],
   "amount" => "9000",           //Note that the amount entered in the "amount" field is in cents
   "orderChannel" => "INTERNET",
   "currencyCode" => "978",
   "keyVersion" => "1",
   "responseEncoding" => "base64",

   "transactionReference" => "m136",
   "paymentPattern" => "INSTALMENT",
   "instalmentData" => array(
      "number" => "3",
      "amountsList" => array('1000','1000','7000'),   //The sum of these amounts must be equal to the content of the amount field
      "datesList" => array('20210205','20210206','20210207'),  //Change the dates according to the time of the test of this use case
      "s10TransactionIdsList" => array($s10TransactionId1,$s10TransactionId2,$s10TransactionId3)   //The first reference must be equal to the one contained in the transactionReference field
   ),
);

$dataStr = flatten_to_sips_payload($requestData);

$dataStrEncode = base64_encode($dataStr);

$_SESSION['seal'] = compute_seal_from_string($_SESSION['sealAlgorithm'], $dataStrEncode, $_SESSION['secretKey']);

$_SESSION['data'] = $dataStrEncode;

header('Location: Common/redirectionForm.php');

?>
