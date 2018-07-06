<?php

class LastUpdateDB {
  
  private $date_modified;
  
  public function __construct($date_modified)
  {
    $this->date_modified = $date_modified;
  }
  
  protected function lastupdateDB()
  {
    $date_modified = $this->date_modified;
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
      
      $stmt = $conn->prepare
      (
        "INSERT INTO lastupdate ( date_modified ) VALUES ( :date_modified )"
      );
   
      $stmt->bindParam(':date_modified', $date_modified, PDO::PARAM_INT);
      $stmt->execute();
    
      echo "UPDATING DATABASE WITH TIMESTAMP OF LAST RECORD..." . "\n";
    }
    
    catch(PDOException $e)
    {
      $pdo_error = $e->getMessage();
# -----------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "CONNECTION FAILED:" . $pdo_error .
        __FILE__ . " ," . __LINE__ . " ," . $date, 3,
        "/var/log/php_credit/error.log"
      );
# -----------------------------------------------------------------------------------------------------------------------------
      echo "CONNECTION FAILED: " . $pdo_error . "\n";
    }
    
    $conn = null;
  }
}

?>