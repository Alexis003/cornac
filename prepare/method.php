<?php

class method extends instruction {

    function __construct($objet) {
        parent::__construct(array());
        
        if (is_array($objet)) {
            $this->objet = $objet[0];
            
            $this->method = $objet[1];
        } else {
            die('Appel de method avec deux arguments? '. join(', ',func_get_args()));
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