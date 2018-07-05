<?php
class GetData {
  function getData($var) {
    global $id,           $status,        $customer,   $email,
           $date_created, $date_modified, $products,   $shipping,
           $tax,          $credit_qty,    $credit_ppu, $creditable,
           $credit_pct,   $credit_issued, $shop_hash,  $headers, $credit_SKU,
           $no_list,
           $yes_list,
           $to_credit;
           
    $id  = (string)$var;
    $url = "";
    $url_order_info = 'https://api.bigcommerce.com/stores/'.$shop_hash.'/v2/orders/'.$id;
    
    $ch = curl_init();
    
    $url = $url_order_info;
    
    curl_setopt_array($ch, array
    (
      CURLOPT_HTTPHEADER      => $headers,
      CURLOPT_URL             => $url,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_FAILONERROR     => true
    ));
    
    $response_info = curl_exec($ch);
  
    if (curl_exec($ch) === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $curl_error    = curl_error($ch);
      $filename = __FILE__;
      $line     = __LINE__;
      error_log("CURL ERROR: $curl_error \n
                             $filename \n
                             $line \n
                             $date \n
                             ORDER: $id \n",
                             3,
                             "/var/log/php_credit/error.log");
      return    "CURL_ERROR";
    }
  
    if (!$response_info || strlen(trim($response_info)) == 0)
    {
      return "REPLY_CONTENT_NULL";
    }
    
    $json_response_info = json_decode($response_info, true);
    
    if ($response_info === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $filename = __FILE__;
      $line     = __LINE__;
      error_log("REPLY CONTENT NOT VALID: $filename \n
                                          $line \n
                                          $date \n
                                          ORDER: $id \n",
                                          3,
                                          "/var/log/php_credit/error.log");
                                          
      return    "REPLY_CONTENT_NOT_VALID";
    }
      
    $customer = (string)$json_response_info['customer_id'];
  
  # -------------------------------------------------------------------------------------------
  
    if ($customer !== "0")
    {
      $url_order_customers = 'https://api.bigcommerce.com/stores/'. $shop_hash.'/v2/customers/'.$customer;
                             
      $url = $url_order_customers;
      
      curl_setopt_array($ch, array(
        CURLOPT_HTTPHEADER      => $headers,
        CURLOPT_URL             => $url,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_FAILONERROR     => true
      ));
      
      $response_customer = curl_exec($ch);
    
      if (curl_exec($ch) === false)
      {
        $date = new DateTime();
        $date = $date->format("y:m:d h:i:s");
        $curl_error    = curl_error($ch);
        $filename = __FILE__;
        $line     = __LINE__;
        error_log("CURL ERROR: $curl_error \n $filename \n $line \n $date \n
                  ORDER: $id \n", 3, "/var/log/php_credit/error.log");
                               
        return    "CURL_ERROR";
      }
    
      if (!$response_customer || strlen(trim($response_customer)) == 0)
      {
        return "REPLY_CONTENT_NULL";
      }
      
      $json_response_customer = json_decode($response_customer, true);
      
      if ($response_customer === false)
      {
        $date = new DateTime();
        $date = $date->format("y:m:d h:i:s");
        $filename = __FILE__;
        $line     = __LINE__;
        error_log("REPLY CONTENT NOT VALID: $filename \n
                                            $line \n
                                            $date \n
                                            ORDER: $id \n",
                                            3,
                                            "/var/log/php_credit/error.log");
                                            
        return    "REPLY_CONTENT_NOT_VALID";
      }
      
      $email         = (string)$json_response_customer['email'];
    }
    
    else
    {
      $customer      = "0";
      $email         = (string)$json_response_info['billing_address']['email'];
    }
  
  # -------------------------------------------------------------------------------------------
  
    $url_order_products  = 'https://api.bigcommerce.com/stores/' . $shop_hash .
                           '/v2/orders/'.$id.'/products';
                           
    $url = $url_order_products;
    
    curl_setopt_array($ch, array
    (
      CURLOPT_HTTPHEADER      => $headers,
      CURLOPT_URL             => $url,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_FAILONERROR     => true
    ));
    
    $response_products = curl_exec($ch);
   
    if (curl_exec($ch) === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $curl_error    = curl_error($ch);
      $filename = __FILE__;
      $line     = __LINE__;
      error_log("CURL ERROR: $curl_error \n
                             $filename \n
                             $line \n
                             $date \n
                             ORDER: $id \n",
                             3,
                             "/var/log/php_credit/error.log");
      return    "CURL_ERROR";
    }
  
    if (!$response_products || strlen(trim($response_products)) == 0)
    {
      return "REPLY_CONTENT_NULL";
    }
    
    $json_response_products = json_decode($response_products, true);
    
    if ($response_products === false)
    {
      $date = new DateTime();
      $date = $date->format("y:m:d h:i:s");
      $filename = __FILE__;
      $line     = __LINE__;
      error_log("REPLY CONTENT NOT VALID: $filename \n
                                          $line \n
                                          $date \n
                                          ORDER: $id \n",
                                          3,
                                          "/var/log/php_credit/error.log");
      return    "REPLY_CONTENT_NOT_VALID";
    }
  
    curl_close($ch);
    
  # -------------------------------------------------------------------------------------------
  
    $id            = (int)$json_response_info['id'];
    $status        = (string)strtolower($json_response_info['custom_status']);
    $date_created  = (int)strtotime($json_response_info['date_created']);
    $date_modified = (int)strtotime($json_response_info['date_modified']);
    $products      = (int)currencyInteger($json_response_info['subtotal_ex_tax']);
    $shipping      = (int)currencyInteger($json_response_info['shipping_cost_ex_tax']);
    $tax	         = (int)currencyInteger($json_response_info['total_tax']);
    
    if ($id == 0)
    {
      return "ORDER_ID_INVALID";
    }
    
    $credit_qty  = 0; #Default value
    $credit_ppu  = 0; #Default value
    $credit_cost = 0; #Default value
    
    foreach($json_response_products as $item) {
      if ($item['sku'] === $credit_SKU) {
        $credit_qty      = (int)$item['quantity'];
        $credit_ppu      = (int)currencyInteger($item['price_ex_tax']);
        $credit_cost     = (int)currencyInteger($item['total_ex_tax']);
      }
    }
    
    $creditable = (int)($products - $credit_cost);
    $to_credit  = (($creditable / 100) * $credit_pct);
    
    if ($credit_qty != 0)
    {
      $credit_qty = $credit_qty * 100;
    }
    
    foreach($no_list as $no_issue) {
      if ($no_issue === $status) {
        $credit_issued = 0;
      }
      else {
        foreach($yes_list as $yes_issue) {
          if ($yes_issue === $status) {
            $credit_issued = $to_credit + $credit_qty;
            $credit_issued = ceil($credit_issued);
            $credit_issued = (int)$credit_issued;
          }
        }
      }
    }
    
    return "READY";
  }
}
?>