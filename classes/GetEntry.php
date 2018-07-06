<?php

class GetEntry
{
  private $id = 0;
  
  public function __construct($id)
  {
    $this->id = $id;
  }
  
  function getEntry($id)
  {
    $id = (string)$id;
    $date = (new DateTime)->format("y:m:d h:i:s");
    
    try
    {
      $conn = new PDO("mysql:host=" . CreditConfig::servername . ";" .
                      "port="       . CreditConfig::port . ";" .
                      "dbname="     . CreditConfig::database . ";" .
                      "charset=utf8",
                       CreditConfig::username,
                       CreditConfig::password,
                       CreditConfig::pdo_options
      );
                       
      $request = $conn->prepare("SELECT * FROM orders WHERE ID=:id");
      $request->bindParam(':id', $id, PDO::PARAM_STR);
      $request->execute();
      $result  = $request->fetch(PDO::FETCH_ASSOC);
    }
    
    catch(PDOException $e)
    {
      $pdo_error = $e->getMessage();
      error_log
      (
        "$curl_error, " . __FILE__ . " ," . __LINE__ . " ," . $date, 3,
        "/var/log/php_credit/error.log"
      );
                                    
      echo "CONNECTION FAILED: " . $e->getMessage() . "\n";
      
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
}
?>