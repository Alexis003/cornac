<?php

class shell extends instruction {
    protected $expression = array();
    
    function __construct($entree) {
        parent::__construct(array());
        
        $this->expression = $entree;
    }

    function __toString() {
        return __CLASS__." `".$this->code."`";
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        foreach($this->expression as &$e) {
            $e->detach();
        }
    }

    static function getRegex(){
        return array('shell_normal_regex'
                    );
    }

}

?>