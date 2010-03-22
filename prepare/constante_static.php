<?php

class constante_static extends token {
    protected $class = null;
    protected $constant = null;
    
    function __construct($entree) {
        parent::__construct();
        
        if (is_array($entree)) {
            $class = new token_traite($entree[0]);
            $class->replace($entree[0]);
            
            $this->class = $class;
            $this->constant = $entree[1];
        } else {
            die('Appel de constant avec deux arguments? '. join(', ',func_get_args()));
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