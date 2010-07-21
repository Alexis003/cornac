<?php

class token_traite extends Token {
    
    function __construct($t) {
        parent::__construct();
        
        if (is_object($t)) {
            $this->copyToken($t);
            $this->setLine($t->getLine());
        } else {
            $this->code = $t;
            $this->setLine(-2);
        }
        mon_log('token_traite');
    }
}

?>