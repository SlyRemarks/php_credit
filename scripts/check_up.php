#!/usr/bin/php

<?php

// RUN BY CRON; UPDATES DATABASE BY THE TIMESTAMP OF THE LAST MODIFIED
// ENTRY IN THE LATEST UPDATE;
// TO RUN ALONG-SIDE THE WEBHOOK METHOD (index.php);

###############################################################################################################################
// Config...
###############################################################################################################################

require_once("../assets/php_credit_config.php");          # Load config file

# -----------------------------------------------------------------------------------------------------------------------------

spl_autoload_register('autoLdr');                         # Load classes

function autoLdr($class) {
  $path = '../classes/';
  require_once $path.$class.'.php';
}

# -----------------------------------------------------------------------------------------------------------------------------

date_default_timezone_set("Europe/London");               # Set timezone
$date_now = date(DateTime::RFC2822);                      # Get current timestamp for BigCommerce API request parameters -

###############################################################################################################################
// Get Timestamp of Latest Update...
###############################################################################################################################

$last_updated = (new MaxModifiedDB)->maxmodifiedDB();     # Get timestamp of last order in previous update, from DB.
$last_updated = "$last_updated";

###############################################################################################################################
// Get Order ID Numbers...
###############################################################################################################################

echo "REQUESTING ORDER ID NUMBERS...\n";
                                                          
$page         = 1;                                        # Paginate the collection.
$limit        = 250;                                      # BigCommerce API returns JSON; page limit is 250 objects (orders).
$list_array   = array();                                  # Each page gets appended to this array on every loop.
$return_value = 0;

while ($return_value == 0)
{
  sleep(1);
  $record_count = 0;
  $query = new QueryBuild($page, $limit,
                          $last_updated, $date_now);
  $query = $query->queryBuild();
  $batch_reply  = (new GetBatch($query))->getBatch();

  if ($batch_reply === "REPLY_CONTENT_NULL")
  {
    break;
  }
  if ($batch_reply === "CURL_ERROR" ||
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
    echo $value_b['id'] . "\n";
    array_push($orders_undone, $value_b['id']);           # Get order ID numbers from the returned array.
  }
}

foreach ($orders_undone as $order)
{
  echo "$order \n";
  $id       = (string)$order;
  $get_data = (new GetData($id))->getData();                # Call the BigCommerce API for order details.
  $counting_orders = $counting_orders++;
  if (++$counting_orders === count($orders_undone))
  {
    $date_of_last = $date_modified;                       # Get the 'date modified' timestamp of the last order in the update.
  }
  if (!empty($get_data))
  {
    echo "TEST \n";
    (new ConnectDB($get_data))->connectDB();              # Enter results in DB.
  }
}

$lastupdateDB = new LastUpdateDB($date_modified);
$lastupdateDB->lastupdateDB();                            # Store 'date modified' timestamp of last order to DB.

echo "UPDATE COMPLETE\n";

# -----------------------------------------------------------------------------------------------------------------------------
# -----------------------------------------------------------------------------------------------------------------------------
?>