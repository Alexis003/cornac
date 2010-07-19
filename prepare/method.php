<?php

class method extends instruction {
    private $objet = null;
    private $method = null;
    
    function __construct($in) {
        parent::__construct(array());
        
        if (is_array($in)) {
            $this->objet = $in[0];
            $this->method = $in[1];
        } else {
            $this->stop_on_error( 'Wrong type of argument');
        }
    }

    function getObject() {  
        return $this->objet;
    }

    function getMethod() {
        return $this->method;
    }

    function neutralise() {
        $this->objet->detach();
        $this->method->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->objet."->".$this->method;
    }

    function getRegex(){
        return array('method_regex',
                     'method_accolade_regex',
                     );
    }

}

?>