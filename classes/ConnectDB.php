<?php
class ConnectDB {
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
      error_log("CONNECTION FAILED: $pdo_error \n
                                    $filename \n
                                    $line \n
                                    $date \n",
                                    3,
                                    "/var/log/php_credit/error.log");
      echo "CONNECTION FAILED: " . $e->getMessage() . "\n";
    }
    
    $conn = null;
  }
}
?>