<?php

class method_static extends instruction {
    protected $class = null;
    protected $method = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (is_array($expression)) {
            $this->class = $this->make_token_traite($expression[0]);
            $this->method = $expression[1];
        } else {
            $this->stop_on_error('Bad call of '.__METHOD__." ".join(', ',func_get_args()));
        }
    }

    function getClass() {  
        return $this->class;
    }

    function getMethod() {
        return $this->method;
    }

    function getCode() {
        return '';
    }

    function neutralise() {
        $this->class->detach();
        $this->method->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->class."::".$this->method;
    }

    function getRegex(){
        return array('method_static_regex',
                     );
    }

}

?>