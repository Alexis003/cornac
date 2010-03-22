<?php

class _throw extends instruction {
    protected $exception = null;
    
    function __construct($expression = null) {
        parent::__construct(array());

        $this->exception = $expression[0];
    }

    function __toString() {
        return __CLASS__." ".$this->exception;
    }

    function getException() {
        return $this->exception;
    }

    function neutralise() {
        $this->exception->detach();
    }

    function getRegex(){
        return array('throw_regex',
                     'throw_parentheses_regex',
                    );
    }

}

?>