#!/usr/bin/php

<?php

// RUN BY CRON; UPDATES DATABASE BY THE TIMESTAMP OF THE LAST MODIFIED
// ENTRY IN THE LATEST UPDATE;
// TO RUN ALONG-SIDE THE WEBHOOK METHOD (index.php);

require_once("assets/config.php");
require_once("php_credit_lib.php");

# -----------------------------------------------------------------------------------------------------------------------------
# -----------------------------------------------------------------------------------------------------------------------------

date_default_timezone_set("Europe/London");

$lastrec_DB   = maxmodifiedDB();                          # Get timestamp of last order in previous update, from DB.
$lastrec_DB   = $lastrec_DB["latest_record"];
$date_now     = date(DateTime::RFC2822);                  # Get current timestamp for BigCommerce API request parameters -
$last_updated = date(DATE_RFC2822, $lastrec_DB);          # converted to required RFC2822 format.

# -----------------------------------------------------------------------------------------------------------------------------

echo "REQUESTING ORDER ID NUMBERS...\n";
                                                          
$page         = 1;                                        # Paginate the collection.
$limit        = 250;                                      # BigCommerce API returns JSON; page limit is 250 objects (orders).
$list_array   = array();                                  # Each page gets appended to this array on every loop.
$return_value = 0;

while ($return_value == 0)
{
  sleep(1);
  $record_count = 0;
  $query        = queryBuild();                           # Construct the GET request for the BigCommerce API.
  $batch_reply  = getBatch();                             # Calling the BigCommerce API: return orders by filters set in query.
  
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
    foreach ($batch_reply as $value)
    {
      $record_count++ ;                                   # Count number of orders on page.
    }
    
    if ($record_count == $limit)                          # If number of orders on page is equal to the limit,
    {                                                     # then request the next page.
      array_push($list_array, $batch_reply);
      $page++;
      echo "RETRIEVING PAGE: $page \n";
    }
    else
    {
      if ($record_count == 0)                             # If number of orders on page is not equal to the limit,
      {                                                   # append these orders to the array (if not 0) and break the loop.
        $return_value = 1;
        break;
      }
      array_push($list_array, $batch_reply);
      $return_value = 1;
      break;
    }
  }
}

echo "RETRIEVING RECORDS...\n";

# -----------------------------------------------------------------------------------------------------------------------------

$orders_undone   = array();
$counting_orders = 0;
$date_of_last    = 0;

foreach ($list_array as $value)
{
  foreach ($value as $value_b)
  {
    array_push($orders_undone, $value_b['id']);           # Get order ID numbers from the returned array.
  }
}

foreach ($orders_undone as $order)
{
  $id       = (string)$order;
  $get_data = getData($id);                               # Call the BigCommerce API for order details.
  $counting_orders = $counting_orders++;
  if(++$counting_orders === count($orders_undone))
  {
    $date_of_last = $date_modified;                       # Get the 'date modified' timestamp of the last order in the update.
  }
  if ($get_data === "READY")
  {
    connectDB();                                          # Enter results in DB.
  }
}

lastupdateDB();                                           # Store 'date modified' timestamp of last order to DB.

echo "UPDATE COMPLETE\n";

# -----------------------------------------------------------------------------------------------------------------------------
# -----------------------------------------------------------------------------------------------------------------------------

?>