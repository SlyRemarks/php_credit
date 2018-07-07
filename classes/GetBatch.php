<?php

class GetBatch
{
  private $query = array();
  
  public function __construct($query)
  {
    $this->query = $query;
  }
  
  public function getBatch()
  {
    $query = $this->query;
    $date = (new DateTime)->format("y:m:d h:i:s");

    $request = curl_init();
    
    $url_order_info = "https://api.bigcommerce.com/stores/" .
                      CreditConfig::shop_hash . "/v2/orders/?" . $query;
    
    curl_setopt($request, CURLOPT_HTTPHEADER    , CreditConfig::headers);
    curl_setopt($request, CURLOPT_URL           , $url_order_info);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_FAILONERROR   , true);
    
    $response_info = curl_exec($request);
  
    if (curl_exec($request) === false)
    {
      $curl_error = curl_error($request);
# -----------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "$curl_error, " . __FILE__ . " ," . __LINE__ . " ," . $date, 3,
        "/var/log/php_credit/error.log"
      );
# -----------------------------------------------------------------------------------------------------------------------------
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
# -----------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "REPLY CONTENT NOT VALID, " . __FILE__ . " ," . __LINE__ . " ," . $date, 3,
        "/var/log/php_credit/error.log"
      );
# -----------------------------------------------------------------------------------------------------------------------------
      return "REPLY_CONTENT_NOT_VALID";
    }
    
    return $response_info;
  }
}

?>