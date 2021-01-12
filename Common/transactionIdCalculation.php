<?php
// Only useful for migration from    Sips 1  to   Sips 2
// This file is used to calculate a default transactionId in case a merchant does not want to provide a transactionId from her/his own system
// Sips will return this transactionId in the field "transactionReference", with format: YYYYMMDD999999, where 999999 is the transactionId.
// The generation of transactionId reproduces the behaviour of Sips 1 APIs distributed after 2018.


function get_s10TransactionReference()
{
   $calculatedId=compute_transactionId();

   $s10TransactionReference = array(
   "s10TransactionId" => $calculatedId,
   );

   return $s10TransactionReference;
}


function compute_transactionId()
{
   // this distributes an Id from 000000 to 999999 along the day, based on typical distribution of transaction during the day.
   $currentDateHour = date('H');
   $currentDateMin = date('i');
   $currentDateSec = date('s');
   $currentDateMillArr = explode(' ', microtime());
   $currentDateMill = (int)round($currentDateMillArr[0] * 1000);
   $intPeriodeMs = 0;
   $intPeriodeMs += $currentDateHour * 60 * 60 * 1000;
   $intPeriodeMs += $currentDateMin * 60 * 1000;
   $intPeriodeMs += $currentDateSec * 1000;
   $intPeriodeMs += $currentDateMill;

   $dblProjectionTid = 0;
   $dblProjectionTid += (-5.636E-26 * pow($intPeriodeMs, 4));
   $dblProjectionTid += (7.061E-18 * pow($intPeriodeMs, 3));
   $dblProjectionTid += (-6.692E-11 * pow($intPeriodeMs, 2));
   $dblProjectionTid += (8.566E-4 * pow($intPeriodeMs, 1));
   $dblProjectionTid = floor($dblProjectionTid);
   $intProjectionTid = (int) $dblProjectionTid;

   if ($intProjectionTid > 999999)
      $intProjectionTid = 999999;
   if ($intProjectionTid < 0)
      $intProjectionTid = 0;

   // Padding
   $strProjectionTid = $intProjectionTid;
   $strProjectionTid=str_pad($strProjectionTid, 6, "0", STR_PAD_LEFT);

   return $strProjectionTid;
}

?>
