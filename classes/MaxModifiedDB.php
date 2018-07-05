<?php
class MaxModifiedDB {
  function maxmodifiedDB() {
    try {
      $conn = new PDO("mysql:host=" . CreditConfig::servername . ";
                       port="       . CreditConfig::port . ";
                       dbname="     . CreditConfig::database,
                       CreditConfig::username,
                       CreditConfig::password,
                       CreditConfig::pdo_options
      );
                       
      $request = $conn->prepare("SELECT MAX(date_modified) as latest_record FROM lastupdate");
      $request->execute();
      $result  = $request->fetch(PDO::FETCH_ASSOC);
    }
    
    catch(PDOException $e) {
      $date      = new DateTime();
      $date      = $date->format("y:m:d h:i:s");
      $pdo_error = $e->getMessage();
      $filename  = __FILE__;
      $line      = __LINE__;
      error_log("CONNECTION FAILED: $pdo_error $filename $line $date", 3,
                                    "/var/log/php_credit/error.log");
     
      echo "CONNECTION FAILED: " . $e->getMessage() . "\n";
      return false;
    }
   
    $conn = null;
    
    if ($result === false) {
      return false;
    }
    else {
      return $result;
    }
  }
}
?>