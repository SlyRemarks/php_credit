<?php

#---------------------------------------------------------------------------------------------------------------------
#-- Include Config File ----------------------------------------------------------------------------------------------

require_once("config.php");

#---------------------------------------------------------------------------------------------------------------------
#-- Catch and Process Post -------------------------------------------------------------------------------------------

$id = 0;
$headers = apache_request_headers();
if (isset($headers)) {
  if ($headers["accesskey"] === $access_key) {
    $body = file_get_contents('php://input');
    if ($body && $body != "") {
      $id_tag = json_decode($body, true);
      if (isset($id_tag["data"]["id"]) && is_int($id_tag["data"]["id"])) {
        $id_tag = $id_tag["data"]["id"];
      }
    }
  }
  else {
    die;
  }
}
else {
  die;
}