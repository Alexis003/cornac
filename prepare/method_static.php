<?php

class method_static extends instruction {
    protected $class = null;
    protected $method = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        if (is_array($entree)) {
            $class = new token_traite($entree[0]);
            $class->replace($entree[0]);
            
            $this->class = $class;
            $this->method = $entree[1];
        } else {
            die('Appel de method avec deux arguments? '. join(', ',func_get_args()));
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