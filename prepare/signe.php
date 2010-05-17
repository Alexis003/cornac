<?php

class signe extends instruction {
    protected $signe = null;
    protected $expression = null;
    
    function __construct($entree = null) {
        parent::__construct(array());

        $this->signe = $this->make_token_traite($entree[0]);
        $this->expression = $entree[1];
    }

    function __toString() {
        return __CLASS__." ".$this->signe.$this->expression;
    }

    function getExpression() {
        return $this->expression;
    }

    function getSigne() {
        return $this->signe;
    }

    function neutralise() {
        $this->signe     ->detach();
        $this->expression->detach();
    }

    function getRegex(){
        return array('signe_regex',
                     'signe_suite_regex',
                    );
    }

}

?>