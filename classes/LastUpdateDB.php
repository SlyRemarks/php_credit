<?php
class LastUpdateDB {
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
    
      echo "UPDATING DATABASE WITH TIMESTAMP OF LAST RECORD..." . "\n";
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