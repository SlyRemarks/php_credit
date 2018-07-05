<?php
class GetLatest {
  function getLatest()
  {
    $request = curl_init();
      
    $url_order_count = "https://api.bigcommerce.com/stores/" . CreditConfig::shop_hash . "/" .
                       "v2/orders/?sort=date_created:desc&limit=1";
    
    curl_setopt($request, CURLOPT_HTTPHEADER    , CreditConfig::headers);
    curl_setopt($request, CURLOPT_URL           , $url_order_count);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_FAILONERROR   , true);
    
    $response_count  = curl_exec($request);
    
    if (curl_exec($request) === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $curl_error = curl_error($request);
# -----------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "WEBHOOK EMPTY BODY," . __FILE__ . "," . __LINE__, 3,
        "/var/log/php_credit/error.log"
      );
# -----------------------------------------------------------------------------------------------------------------------------
      return "CURL_ERROR";
    }
  
    if (!$response_count || strlen(trim($response_count)) == 0)
    {
      return "REPLY_CONTENT_NULL";
    }
    
    $response_count = json_decode($response_count, true);
    
    if ($response_count === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
# ------------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "WEBHOOK EMPTY BODY," . __FILE__ . "," . __LINE__, 3,
        "/var/log/php_credit/error.log"
      );
# -----------------------------------------------------------------------------------------------------------------------------
      return "REPLY_CONTENT_NOT_VALID";
    }
    
    $id_newest = 0;
    $id_newest = (int)$response_count["0"]["id"];
    
    return $id_newest;
  }
}

?>