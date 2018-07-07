<?php

class ConnectDB
{
  private $result = array();
# -----------------------------------------------------------------------------------------------------------------------------
  public function __construct($result)
  {
    $this->result = $result;
  }
# -----------------------------------------------------------------------------------------------------------------------------
  public function connectDB()
  {
    try
    {
      $conn = new PDO
      (
        "mysql:host=" . CreditConfig::servername . ";" .
        "port="       . CreditConfig::port . ";" .
        "dbname="     . CreditConfig::database . ";" .
        "charset=utf8", CreditConfig::username,
                        CreditConfig::password,
                        CreditConfig::pdo_options
      );
      
      $stmt = $conn->prepare(
        
      "INSERT INTO orders (
        id,
        status,
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
   
      $stmt->bindParam(':id'            , $this->result['id']            , PDO::PARAM_INT);
      $stmt->bindParam(':status'        , $this->result['status']        , PDO::PARAM_STR);
      $stmt->bindParam(':customer'      , $this->result['customer']      , PDO::PARAM_STR);
      $stmt->bindParam(':email'         , $this->result['email']         , PDO::PARAM_STR);
      $stmt->bindParam(':date_created'  , $this->result['date_created']  , PDO::PARAM_INT);
      $stmt->bindParam(':date_modified' , $this->result['date_modified'] , PDO::PARAM_INT);
      $stmt->bindParam(':products'      , $this->result['products']      , PDO::PARAM_INT);
      $stmt->bindParam(':shipping'      , $this->result['shipping']      , PDO::PARAM_INT);
      $stmt->bindParam(':tax'           , $this->result['tax']           , PDO::PARAM_INT);
      $stmt->bindParam(':credit_qty'    , $this->result['credit_qty']    , PDO::PARAM_INT);
      $stmt->bindParam(':credit_ppu'    , $this->result['credit_ppu']    , PDO::PARAM_INT);
      $stmt->bindParam(':creditable'    , $this->result['creditable']    , PDO::PARAM_INT);
      $stmt->bindParam(':credit_pct'    , $this->result['credit_pct']    , PDO::PARAM_INT);
      $stmt->bindParam(':credit_issued' , $this->result['credit_issued'] , PDO::PARAM_INT);
      
      $stmt->execute();
    
      echo "< ORDER #" . $this->result['id'] . ": RECORD UPDATED SUCCESSFULLY >" . "\n";
    }
    
    catch(PDOException $e) {
      $date = (new DateTime)->format("y:m:d h:i:s");
      $pdo_error_msg  = $e->getMessage();
      $pdo_error_line = $e->getLine();
      $pdo_error_file = $e->getFile();
      
# -----------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "CONNECTION FAILED: $pdo_error_msg" .
        $pdo_error_file . "," . $pdo_error_line, 3,
        CreditConfig::errorlog . "\n"
      );
# -----------------------------------------------------------------------------------------------------------------------------

      echo "CONNECTION FAILED: " . $pdo_error_msg . "\n";
    }
    
    $conn = null;
  }
}

?>