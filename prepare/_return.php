<?php

class _return extends instruction {
    protected $return = null;

    function __construct($expression = null) {
        parent::__construct(array());

        if (isset($expression[0])) {
            $this->return = $expression[0];
        } 
    }
    
    function __toString() {
        return __CLASS__." return ".$this->return;
    }

    function getReturn() {
        return $this->return;
    }

    function neutralise() {
        if (!is_null($this->return)) {
            $this->return->detach();
        }
    }

    function getRegex() {
        return array(
        'return_simple_regex',
        'return_empty_regex',
);
    }
}

?>