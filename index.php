<?php

// RECEIVE AND PROCESS WEBHOOK:

require_once("assets/config.php");
require_once("php_credit_lib.php");

# -----------------------------------------------------------------------------------------------------------------------------
# -----------------------------------------------------------------------------------------------------------------------------

$id = 0;                                                      # Order ID
$headers_req = apache_request_headers();

if (isset($headers_req))
{
  if ($headers_req["accesskey"] === $access_key)              # Access key is an option when setting the webhook;
  {                                                           # it provides authentication.
    $body = file_get_contents('php://input');
    if ($body && $body != "")
    {
      $id_req = json_decode($body, true);
      if (isset($id_req["data"]["id"]))
      {
        $id = (string)$id_req["data"]["id"];
        $get_data = getData($id);                             # Call the Bigcommerce API.
        if ($get_data === "READY")
        {
          connectDB();
          $date_now = date(DateTime::RFC2822);
          error_log
          (
            "WEBHOOK RECEIVED! $date_now \n",                 # Log successful update.
            3,
            "/var/log/php_credit/check_up.log"
          );
        }
      }
    }
    else
    {
      $filename = __FILE__;                                   # Log error.
      $line     = __LINE__;
      error_log("WEBHOOK EMPTY BODY,
                 $filename, $line",
                 3,
                 "/var/log/php_credit/error.log");
    }
  }
}

?>