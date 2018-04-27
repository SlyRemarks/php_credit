#!/usr/bin/php

<?php

// RUN BY CRON; UPDATES RECORDS IF WEBHOOK METHOD (index.php) FAILS:
// SYMLINK LOCATION: /etc/cron.hourly

require_once("assets/config.php");
require_once("php_credit_lib.php");

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

date_default_timezone_set("Europe/London");
$date_now = date(DateTime::RFC2822);

$last_updated = date(DATE_RFC2822, 1);

echo "DATE NOW: " . $date_now . "\n";

# -------------------------------------------------------------------------------------------

echo "REQUESTING ORDER ID NUMBERS...\n";

$page         = 1;
$limit        = 250;
$list_array   = array();
$return_value = 0;

while ($return_value == 0)
{
  sleep(1);
  $record_count = 0;
  $query        = queryBuild();
  $batch_reply  = getBatch();
  
  if ($batch_reply === "REPLY_CONTENT_NULL")
  {
    break;
  }
  elseif ($batch_reply === "CURL_ERROR" ||
          $batch_reply === "REPLY_CONTENT_NOT_VALID")
  {
    die;
  }
  else
  {
    foreach ($batch_reply as $value) {
      $record_count++ ;
    }
    
    if ($record_count == $limit) {
      array_push($list_array, $batch_reply);
      $page++;
      echo "RETRIEVING PAGE: $page \n";
    }
    else
    {
      array_push($list_array, $batch_reply);
      $return_value = 1;
      break;
    }
  }
}

echo "RETRIEVING RECORDS...\n";

#--------------------------------------------------------------------------------------------------------------------

$orders_undone   = array();
$counting_orders = 0;

foreach ($list_array as $value)
{
  foreach ($value as $value_b)
  {
    array_push($orders_undone, $value_b['id']);
  }
}

foreach ($orders_undone as $order)
{
  $id       = (string)$order;
  $get_data = getData($id);
  $counting_orders = $counting_orders++;
  if(++$counting_orders === count($orders_undone)) {
    echo $date_modified;
  }
  if ($get_data === "READY")
  {
    connectDB();
  }
}

#--------------------------------------------------------------------------------------------------------------------


echo "UPDATE COMPLETE\n";

?>