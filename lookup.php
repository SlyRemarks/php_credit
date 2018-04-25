#!/usr/bin/php

<?php

#  This script is designed to be run by cron.
#  It updates the database to a set schedule,
#  ensuring records are updated should the webhook
#  method fail.

require_once("assets/config.php");
require_once("php_credit_lib.php");

date_default_timezone_set("Europe/London");
$date_now = date(DateTime::RFC2822);

echo "< DATE NOW: >".$date_now."\n";

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
    die;
}

$conn = null;

#--------------------------------------------------------------------------------------------------------------------

$page  = 1;
$limit = 250;
$list_array = array();
$return_value = 0;

while ($return_value == 0)
{
  $query = queryBuild();
  $batch_reply = getBatch();
  $record_count = 0;
  
  if ($batch_reply === "CURL_ERROR" || $batch_reply === "EMPTY_RESPONSE")
  {
    break;
  }
  
  foreach ($batch_reply as $value) {
    $record_count++ ;
  }
  
  if ($record_count == $limit) {
    echo "< RETRIEVING RECORDS >"."\n";
    array_push($list_array, $batch_reply);
    $page++;
  }
  else {
    array_push($list_array, $batch_reply);
    $return_value = 1;
    break;
  }
}

$orders_undone = array();


$counting = 0;
foreach ($list_array as $value) {
  foreach ($value as $valueb) {
    array_push($orders_undone, $valueb['id']);
  }
}
echo $counting;
print_r($orders_undone);

foreach ($orders_undone as $order) {
  $id = (string)$order;
  getData();
  connectDB();
  echo "RECORD $id UPDATED!!!!"."\n";
}

?>