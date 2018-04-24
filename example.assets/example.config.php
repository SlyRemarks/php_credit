<?php

#-------------------------------------------------------

$servername  = "xxxxxxxxxx";
$username    = "xxxxxxxxxx";
$password    = "xxxxxxxxxx";
$database    = "xxxxxxxxxx";
$table       = "xxxxxxxxxx";
$port        = "xxxxxxxxxx";
$pdo_options = [
  PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
];

#-------------------------------------------------------

$client     = "xxxxxxxxxx";
$token      = "xxxxxxxxxx";
$shop_hash  = "xxxxxxxxxx";
$access_key = "xxxxxxxxxx";
$headers    = array(
  "X-Auth-Client:" . $client,
  "X-Auth-Token:"  . $token,
  "Accept:application/json",
  "Content-Type:application/json"
);

#-------------------------------------------------------

$credit_pct = 000000;
$credit_SKU = "xxxxxxxxxx";
  
#-------------------------------------------------------

$no_list =  array(
    
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
);

#-------------------------------------------------------
  
$yes_list =  array(
    
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
  "xxxxxxxxxx",
);

#-------------------------------------------------------

$prompt_pd = "xxxxxxxxxx";

?>