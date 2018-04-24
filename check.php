<?php

#  This script is designed to be run by cron.
#  It updates the database so a set schedule,
#  ensuring records are updated should the webhook
#  method fail.

#---------------------------------------------------------------------------------------------------------------------
#-- Include Config File ----------------------------------------------------------------------------------------------

require_once("assets/config.php");

#---------------------------------------------------------------------------------------------------------------------
#-- Get Current Time -------------------------------------------------------------------------------------------------

date_default_timezone_set("Europe/London");
$date_now = date(DateTime::RFC2822);

#---------------------------------------------------------------------------------------------------------------------
#-- Get Most Recently Updated Record from Local Database -------------------------------------------------------------

try {
  $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password, $pdo_options);
  $request = $conn->prepare("SELECT MAX(date_modified) as latest_record FROM orders");
  $request->execute();
  $result = $request->fetch(PDO::FETCH_ASSOC);
}

catch(PDOException $e) {
    echo "< CONNECTION FAILED! >" . $e->getMessage();
}

$conn = null;

#--------------------------------------------------------------------------------------------------------------------
#-- Build Query -----------------------------------------------------------------------------------------------------

$min_date_modified = $result['latest_record'];
$min_date_modified = date(DATE_RFC2822, $min_date_modified);

$page  = 1;
$limit = 250;

$query = http_build_query([
 'min_date_modified' => (string)$min_date_modified,
 'max_date_modified' => (string)$date_now,
 "limit"             => (string)$limit,
 "page"              => (string)$page,
]);

#---------------------------------------------------------------------------------------------------------------------
#-- HTTP Request -----------------------------------------------------------------------------------------------------

$list_array = array();

$request = curl_init();

$url_order_info = "https://api.bigcommerce.com/stores/$shop_hash/v2/orders/?".$query;

curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
curl_setopt($request, CURLOPT_URL           , $url_order_info);
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($request, CURLOPT_FAILONERROR   , true);

$response_info        = curl_exec($request);

if(!$response_info || strlen(trim($response_info)) == 0) {
  echo 'EMPTY RESPONSE!';
}

if(curl_exec($request) === false) {
  echo "< CURL ERROR: " . curl_error($request) . " >" . "\n";
}

$response_info = json_decode($response_info, true);

$record_count = 0;
foreach ($response_info as $value) {
  $record_count++ ;
}

if ($record_count < $limit) {
  echo "\n";
  echo "limit:".$limit."\n";
  echo "record_count:".$record_count."\n";
  $page++;
  echo "\n";
  echo "page: ".$page;
  echo "\n";
  array_push($list_array, $response_info);
}

curl_close ($request);

$testamount = 0;
foreach ($list_array as $value) {
  foreach ($value as $sp) {
   $testamount++ ;
} }

echo $testamount."\n";

?>