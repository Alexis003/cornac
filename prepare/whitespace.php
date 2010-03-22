<?php

class whitespace extends Token {
    
    function __construct() {
        parent::__construct();
    }

    static public function factory($t) {
        if ($t->getToken() == 371) {
            $retour = $t->getPrev();
            $retour->removeNext();

            return $retour; 
        } else {
            return $t;
        }
    }
}

?>