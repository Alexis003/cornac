<?php

class shell extends instruction {
    protected $expression = array();
    
    function __construct($expression) {
        parent::__construct(array());
        
        // @todo accept anything?
        $this->expression = $expression;
    }

    function __toString() {
        return __CLASS__." `".$this->code."`";
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        foreach($this->expression as $e) {
            $e->detach();
        }
    }

    static function getRegex(){
        return array('shell_normal_regex'
                    );
    }

}

?>