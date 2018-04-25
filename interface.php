#!/usr/bin/php

<?php

#  This script requests the total order count from the shop,
#  then re-populates the database. Existing records may be
#  overwritten but are not deleted.

require_once("assets/config.php");
require_once("php_credit_lib.php");

userPrompt();
$id_newest = getLatest();

for ($id_inc = 7900; $id_inc <= $id_newest ; $id_inc++)
{
  sleep(1);
  $id = (string)$id_inc;
  $foo = getData();
  if ($foo === "not_exist") {
    continue;
  }
  else {
    connectDB();
  }
}


?>