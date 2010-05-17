<?php

class cast extends instruction {
    protected $cast = null;
    protected $expression = null;
    
    function __construct($entree) {
        parent::__construct(array());

        $this->cast = $this->make_token_traite($entree[0]);
        $this->expression = $entree[1];
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getCast() {
        return $this->cast;
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        $this->cast->detach();
        $this->expression->detach();
    }

    function getRegex(){
        return array('cast_normal_regex'
                    );
    }

}

?>