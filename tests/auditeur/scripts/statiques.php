<?php

a::$propriete = 1;

a::method(2);

echo a::constante;

class a {
    static public $propriete = 2;
    
    static function method($a) {
        self::methode2();
        parent::methode3(); 
    } 
    
    const constante = 2;
    
    
}


?>