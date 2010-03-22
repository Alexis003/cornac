<?php

class a{
  public function __set($key, $value)
  {
    $this->value->$key = $value;
  }

}
?>