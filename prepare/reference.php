<?php

class reference extends instruction {
    protected $expression = null;
    
    function __construct($entree) {
        parent::__construct(array());
  
        $this->expression = $entree[0];
//        $this->expression = $this->make_token_traite($entree[0]);
    }

    function __toString() {
        return __CLASS__." &".$this->expression;
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        $this->expression->detach();
    }

    function getRegex(){
        return array('reference_normal_regex'
                    );
    }

}

?>