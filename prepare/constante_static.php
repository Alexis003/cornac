<?php

class constante_static extends token {
    protected $class = null;
    protected $constant = null;
    
    function __construct($expression) {
        parent::__construct();
        
        if (is_array($expression)) {
            $this->class = $this->make_token_traite($expression[0]);
            $this->constant = $expression[1];
        } else {
            $this->stop_on_error("Wrong number of arguments  : '".count($expression)."' in ".__METHOD__);
        }
    }

    function getClass() {  
        return $this->class;
    }

    function getConstant() {
        return $this->constant;
    }

    function neutralise() {
        $this->class->detach();
        $this->constant->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->class."::".$this->constant;
    }

    function getRegex(){
        return array('constante_static_regex',
                     );
    }

}

?>