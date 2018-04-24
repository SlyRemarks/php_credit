<?php

#-----------------------------------------------------------------------------
#-- Config File --------------------------------------------------------------

require_once("config.php");

#-----------------------------------------------------------------------------
#-- Convert Decimals to Integers ---------------------------------------------

function currencyBase($x) {
  if ($x == false) return 0;
  $x = (float)$x;
  if ($x == 0) return 0;
  $x = $x * 100;
  $x = (int)$x;
  return $x;
}

#-----------------------------------------------------------------------------
#-- User Prompt --------------------------------------------------------------

$recreate_input = readline("TYPE '$prompt_pd' TO REFRESH THE DATABASE:  ");

if ($recreate_input === "$prompt_pd"){

$recreate_confirm = readline("ARE YOU SURE? (y/n):  ");
  if ($recreate_confirm === "y") {
    echo "Rebuilding database... " . "\n";

#-----------------------------------------------------------------------------
#-- HTTP Request -------------------------------------------------------------
  
  echo "Retrieving latest order ID" . "\n";
  $request = curl_init();
  
  $url_order_count = "https://api.bigcommerce.com/stores/$shop_hash/" .
                     "v2/orders/?sort=date_created:desc&limit=1";
  
  curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
  curl_setopt($request, CURLOPT_URL           , $url_order_count);
  curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($request, CURLOPT_FAILONERROR   , true);
  
  $response_count  = curl_exec($request);
    
#-----------------------------------------------------------------------------
    
  $response_count = json_decode($response_count, true);
  $id_newest      = 0;
  $id_newest      = (int)$response_count["0"]["id"];
  echo "Latest order ID: $id_newest" . "\n";
    
#-----------------------------------------------------------------------------
#-- Iterate Orders -----------------------------------------------------------
  
    for ($id_inc = 7000; $id_inc <= $id_newest ; $id_inc++) {
    
      sleep(1);
    
      $id = (string)$id_inc;
    
      $request = curl_init();
      
      $url_order_info = "https://api.bigcommerce.com/stores/$shop_hash/v2/orders/$id";
      
      curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
      curl_setopt($request, CURLOPT_URL           , $url_order_info);
      curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($request, CURLOPT_FAILONERROR   , true);
      
      $response_info        = curl_exec($request);
      $json_response_info   = json_decode($response_info, true);
      $customer             = (string)$json_response_info['customer_id'];
      
#-----------------------------------------------------------------------------
    
      $url_order_customers = "https://api.bigcommerce.com/stores/$shop_hash/v2/customers/$customer";
      
      curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
      curl_setopt($request, CURLOPT_URL           , $url_order_customers);
      curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($request, CURLOPT_FAILONERROR   , true);
      
      $response_customer = curl_exec($request);
      
#-----------------------------------------------------------------------------
    
      $url_order_products  = "https://api.bigcommerce.com/stores/$shop_hash/v2/orders/$id/products";
      
      curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
      curl_setopt($request, CURLOPT_URL           , $url_order_products);
      curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($request, CURLOPT_FAILONERROR   , true);
      
      $response_products = curl_exec($request);
      
      if(curl_exec($request) === false) {
        echo "< CURL ERROR: " . curl_error($request) . " >" . "\n";
        curl_close ($request);
        continue;
      }
      
      curl_close ($request);
      
#-----------------------------------------------------------------------------
#-- Decode JSON Response -----------------------------------------------------
    
      $json_response_customer  = json_decode($response_customer, true);
      $json_response_products  = json_decode($response_products, true);
  
#----------------------------------------------------------------------------------------
#-- Parse and Process Response ----------------------------------------------------------
      
      $id            = (int)$json_response_info['id'];
      $status        = (string)strtolower($json_response_info['custom_status']);
      $customer      = (string)$json_response_info['customer_id'];
      $email         = (string)$json_response_customer['email'];
      $date_created  = (int)strtotime($json_response_info['date_created']);
      $date_modified = (int)strtotime($json_response_info['date_modified']);
      $products      = (int)currencyBase($json_response_info['subtotal_ex_tax']);
      $shipping      = (int)currencyBase($json_response_info['shipping_cost_ex_tax']);
      $tax	         = (int)currencyBase($json_response_info['total_tax']);
      
      $credit_qty  = 0; #Default value
      $credit_ppu  = 0; #Default value
      $credit_cost = 0; #Default value
      
      foreach($json_response_products as $item) {
        if ($item['sku'] === $credit_SKU) {
          $credit_qty      = (int)$item['quantity'];
          $credit_ppu      = (int)currencyBase($item['price_ex_tax']);
          $credit_cost     = (int)currencyBase($item['total_ex_tax']);
        }
      }
      
      $creditable    = (int)($products - $credit_cost);
      $to_credit     = (($creditable / 100) * $credit_pct);

#---------------------------------------------------------------------------------------------------------------------
#-- Issue Credits Only if Order Status is Valid ----------------------------------------------------------------------
      
      foreach($no_list as $no_issue) {
        if ($no_issue === $status) {
          $credit_issued = 0;
        }
        else {
          foreach($yes_list as $yes_issue) {
            if ($yes_issue === $status) {
              $credit_issued = (int)ceil($to_credit / 100) + $credit_qty;
            }
          }
        }
      }
      
#---------------------------------------------------------------------------------------------------------------------
#-- Print Results ----------------------------------------------------------------------------------------------------

    /*
      $mask = "%32.32s %-20.20s %-10.10s \n";
      echo("\n");
      printf($mask,"id: "           , $id                      , gettype($id));
      printf($mask,"status: "       , $status                  , gettype($status));
      printf($mask,"customer: "     , $customer                , gettype($customer));
      printf($mask,"email: "        , substr($email,0,10)."...", gettype($email));
      printf($mask,"date_created: " , $date_created            , gettype($date_created));
      printf($mask,"date_modified: ", $date_modified           , gettype($date_modified));
      printf($mask,"products: "     , $products                , gettype($products));
      printf($mask,"shipping: "     , $shipping                , gettype($shipping));
      printf($mask,"tax: "          , $tax                     , gettype($tax));
      printf($mask,"credit_qty: "   , $credit_qty              , gettype($credit_qty));
      printf($mask,"credit_ppu: "   , $credit_ppu              , gettype($credit_ppu));
      printf($mask,"creditable: "   , $creditable              , gettype($creditable));
      printf($mask,"credit_pct: "   , $credit_pct              , gettype($credit_pct));
      printf($mask,"credit_issued: ", $credit_issued           , gettype($credit_issued));
      echo("\n");
    };
     */
#---------------------------------------------------------------------------------------------------------------------
#-- Connect to MySQL -------------------------------------------------------------------------------------------------
    
      try {
        
        $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8", $username, $password, $pdo_options);
        
        echo "< CONNECTED SUCCESSFULLY >" . "\n";
        
        $stmt = $conn->prepare(
          
        "INSERT INTO $table (
          id,  status,
          customer,
          email,
          date_created,
          date_modified,
          products,
          shipping,
          tax,
          credit_qty,
          credit_ppu,
          creditable,
          credit_pct,
          credit_issued
          )
        VALUES (
          :id,
          :status,
          :customer,
          :email,
          :date_created,
          :date_modified,
          :products,
          :shipping,
          :tax,
          :credit_qty,
          :credit_ppu,
          :creditable,
          :credit_pct,
          :credit_issued
          )
        ON DUPLICATE KEY UPDATE
          id=            :id,
          status=        :status,
          customer=      :customer,
          email=         :email,
          date_created=  :date_created,
          date_modified= :date_modified,
          products=      :products,
          shipping=      :shipping,
          tax=           :tax,
          credit_qty=    :credit_qty,
          credit_ppu=    :credit_ppu,
          creditable=    :creditable,
          credit_pct=    :credit_pct,
          credit_issued= :credit_issued"
        );
        
        $stmt->bindParam(':id'           , $id           , PDO::PARAM_INT);
        $stmt->bindParam(':status'       , $status       , PDO::PARAM_STR);
        $stmt->bindParam(':customer'     , $customer     , PDO::PARAM_STR);
        $stmt->bindParam(':email'        , $email        , PDO::PARAM_STR);
        $stmt->bindParam(':date_created' , $date_created , PDO::PARAM_INT);
        $stmt->bindParam(':date_modified', $date_modified, PDO::PARAM_INT);
        $stmt->bindParam(':products'     , $products     , PDO::PARAM_INT);
        $stmt->bindParam(':shipping'     , $shipping     , PDO::PARAM_INT);
        $stmt->bindParam(':tax'          , $tax          , PDO::PARAM_INT);
        $stmt->bindParam(':credit_qty'   , $credit_qty   , PDO::PARAM_INT);
        $stmt->bindParam(':credit_ppu'   , $credit_ppu   , PDO::PARAM_INT);
        $stmt->bindParam(':creditable'   , $creditable   , PDO::PARAM_INT);
        $stmt->bindParam(':credit_pct'   , $credit_pct   , PDO::PARAM_INT);
        $stmt->bindParam(':credit_issued', $credit_issued, PDO::PARAM_INT);
        
        $stmt->execute();
      
        echo "< ORDER #" . "$id" . ": RECORD UPDATED SUCCESSFULLY >" . "\n";
      }
      
      catch(PDOException $e) {
        echo "< CONNECTION FAILED! >" . $e->getMessage() . "\n";
      }
      
      $conn = null;
    }
  }
  die;
}
die;

?>