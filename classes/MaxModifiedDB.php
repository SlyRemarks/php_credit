<?php

class MaxModifiedDB
{
  function maxmodifiedDB()
  {
    $date    = (new DateTime)->format("y:m:d h:i:s");
    $default = date(DATE_RFC2822, 1);
    try
    {
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
      return $default;
    }
    $conn = null;
# -----------------------------------------------------------------------------------------------------------------------------
    if ($result === false) {
      return $default;
    }
    elseif (isset($result["latest_record"]))
    {
      $result = $result["latest_record"];
      if (is_int($result))
      {
        $result = date(DATE_RFC2822, $result); # converted to required RFC2822 format.
        return $result;
      }
      else
      {
        return $default;
      }
    }
    else
    {
      return $default;
    }
  }
}

?>