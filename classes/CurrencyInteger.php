<?php
class CurrencyInteger {
  function currencyInteger($var)
  {
    if ($var === false)
    {
      return 0;
    }
    $var = (float)$var;
    if ($var == 0)
    {
      return 0;
    }
    $var = $var * 100;
    $var = (int)$var;
    return $var;
  }
}
?>