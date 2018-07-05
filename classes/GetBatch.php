<?php
class GetBatch {
  function getBatch() {
    global $shop_hash,
           $query,
           $headers;
    
    $request = curl_init();
    
    $url_order_info = "https://api.bigcommerce.com/stores/$shop_hash/v2/orders/?".$query;
    
    curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
    curl_setopt($request, CURLOPT_URL           , $url_order_info);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_FAILONERROR   , true);
    
    $response_info = curl_exec($request);
  
    if (curl_exec($request) === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $curl_error    = curl_error($request);
      $filename = __FILE__;
      $line     = __LINE__;
      error_log("CURL ERROR: $curl_error \n
                             $filename \n
                             $line \n
                             $date \n",
                             3,
                             "/var/log/php_credit/error.log");
      return "CURL_ERROR";
    }
  
    if (!$response_info || strlen(trim($response_info)) == 0)
    {
      return "REPLY_CONTENT_NULL";
    }
    
    curl_close ($request);
    
    $response_info = json_decode($response_info, true);
    
    if ($response_info === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $filename = __FILE__;
      $line     = __LINE__;
      error_log("REPLY CONTENT NOT VALID: $filename \n
                                          $line \n
                                          $date \n",
                                          3,
                                          "/var/log/php_credit/error.log");
                                          
      return "REPLY_CONTENT_NOT_VALID";
    }
    
    return $response_info;
  }
}
?>