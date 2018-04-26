#!/usr/bin/php

<?php

// UI FOR UPDATING AND INSPECTING RECORDS MANUALLY:

require_once("assets/config.php");
require_once("php_credit_lib.php");

function userPrompt()
{
  echo "\n";
  echo "TYPE 'lookup'  TO VIEW ENTRY\n";
  echo "TYPE 'update'  TO UPDATE ENTRY\n";
  echo "TYPE 'rebuild' TO REFRESH WHOLE TABLE\n";
  echo "TYPE 'exit'    TO EXIT\n";
  echo "\n";
  
  $user_prompt = readline("> ");
  
  switch ($user_prompt) {
      case "lookup":
          echo "VIEW ENTRY: ARE YOU SURE? (y/n)\n";
          break;
      case "update":
          echo "UPDATE ENTRY: ARE YOU SURE? (y/n)\n";
          break;
      case "rebuild":
          echo "REBUILD TABLE: ARE YOU SURE? (y/n)\n";
          break;
      case "exit":
          break;
      default:
          break;
  }
}

$return_prompt = userPrompt();


/*

$id_newest = getLatest();
updateAll();


# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function viewRecord()
{
  echo "view single record...";
}


# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function updateOne()
{
  echo "update single record...";
}


# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function updateAll()
{
  global $id_newest;
  
  if ($id_newest !== "CURL_ERROR" ||
      $id_newest !== "REPLY_CONTENT_NULL" ||
      $id_newest !== "REPLY_CONTENT_NOT_VALID")
  {
    for($id_inc = 100; $id_inc <= $id_newest ; $id_inc++)
    {
      sleep(1);
      $id = (string)$id_inc;
      $get_data = getData($id);
      if($get_data === "READY")
      {
        connectDB();
      }
      else {
        continue;
      }
    }
  }
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
*/
?>