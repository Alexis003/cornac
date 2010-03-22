<?php

class property_static extends token {
    protected $class = null;
    protected $property = null;
    
    function __construct($entree) {
        parent::__construct();
        
        if (is_array($entree)) {
            $class = new token_traite($entree[0]);
            $class->replace($entree[0]);
            
            $this->class = $class;
            $this->property = $entree[1];
        } else {
            die('Appel de constant avec deux arguments? '. join(', ',func_get_args()));
        }
    }

    function getClass() {  
        return $this->class;
    }

    function getProperty() {
        return $this->property;
    }

    function neutralise() {
        $this->class->detach();
        $this->property->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->class."::".$this->property;
    }

    function getRegex(){
        return array('property_static_regex',
                     );
    }

}

?>