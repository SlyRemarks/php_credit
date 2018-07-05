<?php

class CreditConfig {
  
  const servername     = "xxxxxxxxxxxxxxxxxxxxxxxx",
        username       = "xxxxxxxxxxxxxxxxxxxxxxxx",
        password       = "xxxxxxxxxxxxxxxxxxxxxxxx",
        database       = "xxxxxxxxxxxxxxxxxxxxxxxx",
        table          = "xxxxxxxxxxxxxxxxxxxxxxxx",
        port           = "xxxxxxxxxxxxxxxxxxxxxxxx",
        pdo_options    = [
          PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
        ],
        
  ##################################################################
        
        wh_scope       = 'xxxxxxxxxxxxxxxxxxxxxxxx',
        wh_destination = 'xxxxxxxxxxxxxxxxxxxxxxxx',
        wh_is_active   = 1,
        shop_hash      = "xxxxxxxxxxxxxxxxxxxxxxxx",
        access_key     = "xxxxxxxxxxxxxxxxxxxxxxxx",
        webhook_id     = "xxxxxxxxxxxxxxxxxxxxxxxx",
        headers        = array(
          "X-Auth-Client:" . "xxxxxxxxxxxxxxxxxxxxxxxx",
          "X-Auth-Token:"  . "xxxxxxxxxxxxxxxxxxxxxxxx",
          "Accept:application/json",
          "Content-Type:application/json"
        ),
      
  ##################################################################
      
        credit_pct = 20,
        credit_SKU = "xxxxxxxxxxxxxxxxxxxxxxxx",
      
        no_list = array(
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        ),
      
      
        yes_list =  array(
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
        "xxxxxxxxxxxxxxxxxxxxxxxx",
  );
}

?>