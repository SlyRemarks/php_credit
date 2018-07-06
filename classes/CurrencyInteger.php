<?php

class CurrencyInteger {
  
  private $num = 0;
  
  public function __construct($num)
  {
    $this->num = $num;
  }
  
  function currencyInteger($num)
  {
    if ($num === false)
    {
      return 0;
    }
    $num = (float)$num;
    if ($num == 0)
    {
      return 0;
    }
    $num = $num * 100;
    $num = (int)$num;
    return $num;
  }
}

?>