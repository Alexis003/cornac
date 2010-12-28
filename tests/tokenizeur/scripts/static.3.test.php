<?php


class c { }
class b extends c { 
    function __construct($x, $y) {}
    function m() {
     $y = new self( 1);
     $x = new static();
   }

}
class a extends b {
}

?>