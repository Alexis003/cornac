<?php
class x
{
    static public function & a(&$x){} 
    static private function & b(&$x){} 
    static protected function & c(&$x){} 
}
?>