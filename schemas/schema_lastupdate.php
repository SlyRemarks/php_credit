<?php

// UPDATE SCHEMA FOR TABLE 'LASTUPDATE' IN DATABASE:

require_once("../assets/php_credit_config.php");

try
{
  $conn = new PDO
  (
    "mysql:host=" . CreditConfig::servername . ";" .
                  "port="       . CreditConfig::port . ";" .
                  "dbname="     . CreditConfig::database . ";" .
                  "charset=utf8",
                   CreditConfig::username,
                   CreditConfig::password,
                   CreditConfig::pdo_options
  );

  echo "< CONNECTED SUCCESSFULLY >" . "\n";

  $sql =
    "DROP TABLE IF EXISTS lastupdate;
    CREATE TABLE lastupdate (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date_modified INT(10)
    )";
  
  $conn->exec($sql);
  echo "Table 'lastupdate' created successfully" . "\n";
}
    
catch(PDOException $e)
{
  echo "\n";
  echo "< CONNECTION FAILED: " . $e->getMessage() . " >";
}

$conn = null;

?>