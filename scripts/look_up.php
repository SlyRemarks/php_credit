#!/usr/bin/php

<?php

// UI FOR UPDATING AND INSPECTING RECORDS MANUALLY;

###############################################################################################################################
###############################################################################################################################

require_once("../assets/php_credit_config.php");

# -----------------------------------------------------------------------------------------------------------------------------

spl_autoload_register('autoLdr');                         # Load classes

function autoLdr($class) {
  $path = '../classes/';
  require_once $path.$class.'.php';
}

###############################################################################################################################
###############################################################################################################################

function updateOne($val)
{
  $id = (string)$val;
  $get_data = getData($id);
  if($get_data === "READY")
  {
    connectDB();
  }
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

function updateAll()
{
  $id_newest = getLatest();

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
          return "lookup";
          break;
      case "update":
          return "update";
          break;
      case "rebuild":
          return "rebuild";
          break;
      case "exit":
          break;
      default:
          break;
  }
}

# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------

$return_prompt = userPrompt();

echo "\n";

if ($return_prompt === "lookup")
{
  echo "ORDER ID NUMBER? \n";
  echo "\n";
  $user_prompt = readline("> ");
  echo "\n";
  $user_prompt = (int)$user_prompt;
  if (($user_prompt >= 0) && ($user_prompt <= 1000000000))
  {
    $id = (string)$user_prompt;
    getEntry($id);
  }
}

if ($return_prompt === "update")
{
  echo "ORDER ID NUMBER? \n";
  echo "\n";
  $user_prompt = readline("> ");
  echo "\n";
  $user_prompt = (int)$user_prompt;
  if (($user_prompt >= 0) && ($user_prompt <= 1000000000))
  {
    $id = (string)$user_prompt;
    updateOne($id);
  }
}

if ($return_prompt === "rebuild")
{
  echo "REBUILD TABLE - ARE YOU SURE? (y/n): \n";
  echo "\n";
  $user_prompt = readline("> ");
  echo "\n";
  if ($user_prompt === "y")
  {
    updateAll();
  }
  else
  {
    die;
  }
}

echo "\n";
echo "END\n"

?>