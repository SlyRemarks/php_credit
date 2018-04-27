<?php

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function maxmodifiedDB()
{
  global $servername, $port,
         $database,   $username,
         $password,   $pdo_options;
  
  try
  {
    $conn = new PDO("mysql:host=$servername;
                     port=$port;
                     dbname=$database",
                     $username,
                     $password,
                     $pdo_options);
                     
    $request = $conn->prepare("SELECT MAX(date_modified) as latest_record FROM lastupdate");
    $request->execute();
    $result  = $request->fetch(PDO::FETCH_ASSOC);
  }
  
  catch(PDOException $e)
  {
    $date = new DateTime();
    $date = $date->format("y:m:d h:i:s");
    $pdo_error = $e->getMessage();
    $filename = __FILE__;
    $line     = __LINE__;
    error_log("CONNECTION FAILED: $pdo_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    echo      "CONNECTION FAILED: " . $e->getMessage() . "\n";
    return "FAILED";
  }
 
  $conn = null;
  
  if ($result === false)
  {
    return 1;
  }
  else
  {
    return $result;
  }
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function getBatch()
{
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
    error_log("CURL ERROR: $curl_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    return    "CURL_ERROR";
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
    error_log("REPLY CONTENT NOT VALID: $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    return    "REPLY_CONTENT_NOT_VALID";
  }
  
  return $response_info;
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function queryBuild()
{
  global $result,
         $min_date_modified,
         $date_now,
         $limit,
         $page,
         $last_updated;
  
  $min_date_modified = $last_updated;

  $query = http_build_query([
   'min_date_modified' => (string)$last_updated,
   'max_date_modified' => (string)$date_now,
   "limit"             => (string)$limit,
   "page"              => (string)$page,
  ]);
  
  return $query;
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function currencyInteger($var)
{
  if ($var === false)
  {
    return 0;
  }
  $var = (float)$var;
  if ($var == 0)
  {
    return 0;
  }
  $var = $var * 100;
  $var = (int)$var;
  return $var;
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function getLatest()
{
  global $shop_hash,
         $headers;
         
  $request = curl_init();
    
  $url_order_count = "https://api.bigcommerce.com/stores/$shop_hash/" .
                     "v2/orders/?sort=date_created:desc&limit=1";
  
  curl_setopt($request, CURLOPT_HTTPHEADER    , $headers);
  curl_setopt($request, CURLOPT_URL           , $url_order_count);
  curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($request, CURLOPT_FAILONERROR   , true);
  
  $response_count  = curl_exec($request);
  
  if (curl_exec($request) === false)
  {
    $date = new DateTime();
    $date = $date->format("y:m:d h:i:s");
    $curl_error    = curl_error($request);
    $filename = __FILE__;
    $line     = __LINE__;
    error_log("CURL ERROR: $curl_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
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
    $filename = __FILE__;
    $line     = __LINE__;
    error_log("REPLY CONTENT NOT VALID: $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    return "REPLY_CONTENT_NOT_VALID";
  }
  
  $id_newest      = 0;
  $id_newest      = (int)$response_count["0"]["id"];
  
  return $id_newest;
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function getData($var)
{
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
    error_log("CURL ERROR: $curl_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
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
    error_log("REPLY CONTENT NOT VALID: $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    return    "REPLY_CONTENT_NOT_VALID";
  }
    
  $customer = (string)$json_response_info['customer_id'];

# -------------------------------------------------------------------------------------------
  
  $url_order_customers = 'https://api.bigcommerce.com/stores/'.$shop_hash.'/v2/customers/'.$customer;
  $url = $url_order_customers;
  
    curl_setopt_array($ch, array
  (
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
    error_log("CURL ERROR: $curl_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
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
    error_log("REPLY CONTENT NOT VALID: $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    return    "REPLY_CONTENT_NOT_VALID";
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
    error_log("CURL ERROR: $curl_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
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
    error_log("REPLY CONTENT NOT VALID: $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    return    "REPLY_CONTENT_NOT_VALID";
  }

  curl_close($ch);
  
# -------------------------------------------------------------------------------------------

  $id            = (int)$json_response_info['id'];
  $status        = (string)strtolower($json_response_info['custom_status']);
  $customer      = (string)$json_response_info['customer_id'];
  $email         = (string)$json_response_customer['email'];
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

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function connectDB()
{
  global $id,           $status,        $customer,   $email,
         $date_created, $date_modified, $products,   $shipping,
         $tax,          $credit_qty,    $credit_ppu, $creditable,
         $credit_pct,   $credit_issued, $servername, $port,
         $database,     $username,      $password,   $pdo_options;
  try
  {
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8",
                     $username, $password, $pdo_options);
    
    $stmt = $conn->prepare(
      
    "INSERT INTO orders (
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
    $date = new DateTime();
    $date = $date->format("y:m:d h:i:s");
    $pdo_error = $e->getMessage();
    $filename = __FILE__;
    $line     = __LINE__;
    error_log("CONNECTION FAILED: $pdo_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    echo      "CONNECTION FAILED: " . $e->getMessage() . "\n";
  }
  
  $conn = null;
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function getEntry($val)
{
  global $servername, $port,
         $database,   $username,
         $password,   $pdo_options;
         
  $id = $val;
  
  try
  {
    $conn = new PDO("mysql:host=$servername;
                     port=$port;
                     dbname=$database",
                     $username,
                     $password,
                     $pdo_options);
                     
    $request = $conn->prepare("SELECT * FROM orders WHERE ID=:id");
    $request->bindParam(':id', $id, PDO::PARAM_STR);
    $request->execute();
    $result  = $request->fetch(PDO::FETCH_ASSOC);
  }
  
  catch(PDOException $e)
  {
    $date = new DateTime();
    $date = $date->format("y:m:d h:i:s");
    $pdo_error = $e->getMessage();
    $filename = __FILE__;
    $line     = __LINE__;
    error_log("CONNECTION FAILED: $pdo_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    echo      "CONNECTION FAILED: " . $e->getMessage() . "\n";
    return "FAILED";
  }
  
  if(empty($result))
  {
    echo "NO RECORD FOUND\n";
    die;
  }
 
  $conn = null;

  $id            = $result['id'];
  $status        = $result['status'];
  $customer      = $result['customer'];
  $email         = $result['email'];
  $date_created  = $result['date_created'];
  $date_modified = $result['date_modified'];
  $products      = $result['products'];
  $shipping      = $result['shipping'];
  $tax           = $result['tax'];
  $credit_qty    = $result['credit_qty'];
  $credit_ppu    = $result['credit_ppu'];
  $creditable    = $result['creditable'];
  $credit_pct    = $result['credit_pct'];
  $credit_issued = $result['credit_issued'];
  
  $mask = "%32.32s %-60.60s \n";
  echo("\n");
  printf($mask,"id: "           , $id);
  printf($mask,"status: "       , $status);
  printf($mask,"customer: "     , $customer);
  printf($mask,"email: "        , substr($email,0,60));
  printf($mask,"date_created: " , $date_created);
  printf($mask,"date_modified: ", $date_modified);
  printf($mask,"products: "     , $products);
  printf($mask,"shipping: "     , $shipping);
  printf($mask,"tax: "          , $tax);
  printf($mask,"credit_qty: "   , $credit_qty);
  printf($mask,"credit_ppu: "   , $credit_ppu);
  printf($mask,"creditable: "   , $creditable);
  printf($mask,"credit_pct: "   , $credit_pct);
  printf($mask,"credit_issued: ", $credit_issued);
  echo("\n");
}
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function lastupdateDB()
{
  global $date_modified, $servername, $port,
         $database,     $username,      $password,   $pdo_options;
         
  try
  {
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8",
                     $username, $password, $pdo_options);
    
    $stmt = $conn->prepare(
      
    "INSERT INTO lastupdate
      (
      date_modified
      )
    VALUES
      (
      :date_modified
      )"
    );
 
    $stmt->bindParam(':date_modified', $date_modified, PDO::PARAM_INT);

    $stmt->execute();
  
    echo "UPDATING DATABASE WITH TIMESTAMP OF UPDATE..." . "\n";
  }
  
  catch(PDOException $e) {
    $date = new DateTime();
    $date = $date->format("y:m:d h:i:s");
    $pdo_error = $e->getMessage();
    $filename = __FILE__;
    $line     = __LINE__;
    error_log("CONNECTION FAILED: $pdo_error \n $filename \n $line \n $date \n", 3, "/var/log/php_credit/error.log");
    echo      "CONNECTION FAILED: " . $e->getMessage() . "\n";
  }
  
  $conn = null;
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

?>
