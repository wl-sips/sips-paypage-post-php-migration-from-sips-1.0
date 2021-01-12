<?php
/*This file generates the payment request and sends it to the Sips server
For more information on this use case, please refer to the following documentation:
https://documentation.sips.worldline.com/en/WLSIPS.344-UG-3-D-Secure.html */

session_start();
   
include('Common/sealCalculationPaypagePost.php');
include('Common/flatten.php');
include('Common/transactionIdCalculation.php');

//PAYMENT REQUEST

// parameters.php initializes some session data like $_SESSION['merchantId'], $_SESSION['secretKey'], $_SESSION['normalReturnUrl'] and $_SESSION["urlForPaymentInitialisation"]
// You can change these values in parameters.php according to your needs and architecture
include('parameters.php');


// Merchants migrating from WL Sips 1.0 to WL Sips 2.0 must provide a transactionId. This easily done below. (second example used as default).

// Example with the merchant's own transactionId (typically when you increment Ids from your database)
// $s10TransactionReference=array(
//    "s10TransactionId" => "000001",
// //   "s10TransactionIdDate" => "not needed",   Please note that the date is not needed, Sips server will apply its date.
// );
//
// Example with transactionId automatic generation, like the WL Sips 1.0 API was doing.
$s10TransactionReference=get_s10TransactionReference();


$requestData = array(
   "normalReturnUrl" => $_SESSION['normalReturnUrl'],
   "merchantId" => $_SESSION['merchantId'],
   "amount" => "2000",           //Note that the amount entered in the "amount" field is in cents
   "orderChannel" => "INTERNET",
   "currencyCode" => "978",
   "keyVersion" => "1",
   "responseEncoding" => "base64",
   
   "s10TransactionReference" => $s10TransactionReference,

   "billingAddress" => array(
      "city" => "Nantes",
      "country" => "FRA",
      "addressAdditional1" => "route de l'atlantique, 5990",
      "addressAdditional2" => "rue Pompidou, 8900",
      "addressAdditional3" => "avenue Jean Jaures, 4900",
      "zipCode" => "44000",
      "state" => "France",
   ),
   "holderContact" => array(
      "lastname" => "Doe",
      "email" => "jane.doe@example.org",
   ),
   "fraudData" => array(
      "merchantCustomerAuthentMethod" => "NOAUTHENT",
      "challengeMode3DS" => "NO_CHALLENGE",
   ),
);

$dataStr = flatten_to_sips_payload($requestData);

$dataStrEncode = base64_encode($dataStr);

$_SESSION['seal'] = compute_seal_from_string($_SESSION['sealAlgorithm'], $dataStrEncode, $_SESSION['secretKey']);

$_SESSION['data'] = $dataStrEncode;

header('Location: Common/redirectionForm.php');

?>
