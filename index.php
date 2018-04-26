<?php

// RECEIVE AND PROCESS WEBHOOK:

require_once("assets/config.php");
require_once("php_credit_lib.php");

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

$id = 0;
$headers_req = apache_request_headers();

if (isset($headers_req))
{
  if ($headers_req["accesskey"] === $access_key)
  {
    $body = file_get_contents('php://input');
    if ($body && $body != "")
    {
      $id_req = json_decode($body, true);
      if (isset($id_req["data"]["id"]))
      {
        $id = (string)$id_req["data"]["id"];
        $get_data = getData($id);
        if ($get_data === "READY")
        {
         connectDB();
        }
      }
    }
    else
    {
      error_log("WEBHOOK EMPTY BODY", 3, "error.log");
    }
  }
}

?>
