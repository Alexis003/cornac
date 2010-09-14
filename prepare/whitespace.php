<?php

class whitespace extends Token {
    
    function __construct() {
        parent::__construct();
    }

    static public function factory($t) {
        if ($t->getToken() == T_WHITESPACE) {
            $return = $t->getPrev();
            $return->removeNext();

            return $return; 
        } else {
            return $t;
        }
    }
}

?>