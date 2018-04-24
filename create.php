<?php

# Update schema for table in database.

#---------------------------------------------------------------------------------------------------------------------
#-- Include Config File ----------------------------------------------------------------------------------------------

require_once("assets/config.php");

#---------------------------------------------------------------------------------------------------------------------
#-- HTTP Request -----------------------------------------------------------------------------------------------------

try {
  $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password, $pdo_options);

  echo "< CONNECTED SUCCESSFULLY >" . "\n";

  $sql =
    "DROP TABLE IF EXISTS $table;
    CREATE TABLE $table (
    id INT(10) UNSIGNED PRIMARY KEY,
    status VARCHAR(50),
    customer VARCHAR(10),
    email VARCHAR(255),
    date_created INT(10),
    date_modified INT(10),
    products INT(10),
    shipping INT(10),
    tax INT(10),
    credit_qty INT(10),
    credit_ppu INT(10),
    creditable INT(10),
    credit_pct INT(3),
    credit_issued INT(10)
    )";
  
  $conn->exec($sql);
  echo "Table [$table] created successfully" . "\n";
}
    
catch(PDOException $e) {
  echo "\n";
  echo "< CONNECTION FAILED: " . $e->getMessage() . " >";
}

$conn = null;

?>