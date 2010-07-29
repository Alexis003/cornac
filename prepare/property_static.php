<?php

class property_static extends token {
    protected $class = null;
    protected $property = null;
    
    function __construct($entree) {
        parent::__construct();
        
        if (is_array($entree)) {
            $this->class = $this->make_token_traite($entree[0]);
            $this->property = $entree[1];
        } else {
            $this->stop_on_error("Wrong number of arguments  : '".count($entree)."' in ".__METHOD__);
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