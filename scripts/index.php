<?php

// RECEIVE AND PROCESS WEBHOOK;

###############################################################################################################################
###############################################################################################################################

require_once("../assets/php_credit_config.php");              # Load config

# -----------------------------------------------------------------------------------------------------------------------------

spl_autoload_register('autoLdr');                             # Load classes

function autoLdr($class) {
  $path = '../classes/';
  require_once $path.$class.'.php';
}

###############################################################################################################################
###############################################################################################################################

$id = 0;                                                      # Order ID
$headers_req = apache_request_headers();

if (isset($headers_req))
{
  if (CreditConfig::$access_key === $access_key)              # Access key is an option when setting the webhook;
  {                                                           # it provides authentication.
    $body = file_get_contents('php://input');
    if ($body && $body != "")
    {
      $id_req = json_decode($body, true);
      if (isset($id_req["data"]["id"]))
      {
        $id = (string)$id_req["data"]["id"];
        $get_data = (new GetData)->getData($id);              # Call the Bigcommerce API.
        if ($get_data === "READY")
        {
          $connectDB = (new ConnectDB)->connectDB();
          $date_now  = date(DateTime::RFC2822);
# -----------------------------------------------------------------------------------------------------------------------------
          error_log
          (
            "WEBHOOK RECEIVED! $date_now", 3,                 # Log successful update.
            "/var/log/php_credit/check_up.log"
          );
# ------------------------------------------------------------------------------------------------------------------------------
        }
      }
    }
    else
    {
# ------------------------------------------------------------------------------------------------------------------------------
      error_log
      (
        "WEBHOOK EMPTY BODY," . __FILE__ . "," . __LINE__, 3,
        "/var/log/php_credit/error.log"
      );
# -----------------------------------------------------------------------------------------------------------------------------
    }
  }
}

?>