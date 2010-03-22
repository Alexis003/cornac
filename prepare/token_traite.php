<?php

class token_traite extends Token {
    
    function __construct($t) {
        parent::__construct();
        
        if (is_object($t)) {
            $this->copyToken($t);
        } else {
            $this->code = $t;
        }
    }
}

?>