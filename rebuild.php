#!/usr/bin/php

<?php

#  This script requests the total order count from the shop,
#  then re-populates the database. Existing records may be
#  overwritten but are not deleted otherwise.

require_once("assets/config.php");
require_once("functions.php");

userPrompt();

#-----------------------------------------------------------------------------

echo "Retrieving latest order ID" . "\n";
$request = curl_init();

$url_order_count = "https://api.bigcommerce.com/stores/$shop_hash/" .
                   "v2/orders/?sort=date_created:desc&limit=1";

curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
curl_setopt($request, CURLOPT_URL           , $url_order_count);
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($request, CURLOPT_FAILONERROR   , true);

$response_count  = curl_exec($request);
$response_count = json_decode($response_count, true);
$id_newest      = 0;
$id_newest      = (int)$response_count["0"]["id"];
echo "Latest order ID: $id_newest" . "\n";
    
#-----------------------------------------------------------------------------

for ($id_inc = 8999; $id_inc <= $id_newest ; $id_inc++)
{
  sleep(1);
  $id = (string)$id_inc;
  $foo = getData();
  if ($foo === "not_exist") {
    continue;
  }
  else {
  connectDB();
  }
}


?>