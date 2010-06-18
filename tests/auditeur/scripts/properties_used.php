<?php

class x {
    static $b = 1;
    
    function test() {
        $this->b = 1;
        $this->a++;
        
        $this->method($this->c, $this->d, $this->e);
        x::$b = 3;
        
        $autre->am = 2;
    }
}

$x = new x();
$x->a = 1;
x::$b = 2;

?>