<?php

class _break extends instruction {
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        if (!isset($expression[1])) {
            $this->niveaux = new token_traite(1);
        } elseif ($expression[1]->checkClass('parentheses')) {
            $this->niveaux =  new token_traite($expression[1]->getContenu()->getCode());
        } else {
            $this->niveaux =  new token_traite($expression[1]->getCode());
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getNiveaux() {
        return $this->niveaux;
    }

    function neutralise() {
    }

    function getRegex(){
        return array('break_alone_regex',
                     'break_leveled_regex',
                    );
    }

}

?>