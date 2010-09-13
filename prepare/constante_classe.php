<?php

class constante_classe extends token {
    protected $name = null;
    protected $constante = null;
    
    function __construct($expression) {
        parent::__construct();
        
        if (is_array($expression) && count($expression) == 2) {
            $this->name = $expression[0];
            $this->constante = $expression[1];
        } else {
            $this->stop_on_error("Wrong number of arguments  : '".count($expression)."' in ".__METHOD__);
        }
    }

    function getName() {  
        return $this->name;
    }

    function getConstante() {
        return $this->constante;
    }

    function neutralise() {
        $this->name->detach();
        $this->constante->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->name."::".$this->constante;
    }

    function getRegex(){
        return array('constante_classe_regex',
                     );
    }

}

?>