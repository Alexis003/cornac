<?php

class property extends token { 
    private $objet = null;
    private $property = null;

    function __construct($expression) {
        parent::__construct();
        
        if (is_array($expression) && count($expression) == 2) {
            if ($expression[0]->checkClass('Token')) {
                $objet = new token_traite($expression[0]);
                $objet->replace($expression[0]);
            } else {
                $objet = $expression[0];
            }

            $this->objet = $objet;
            
            $this->property = $expression[1];
        } else {
            $this->stop_on_error("Bad number of parameters in ".__METHOD__);
        }
    }

    function getObject() {  
        return $this->objet;
    }

    function getProperty() {
        return $this->property;
    }

    function neutralise() {
        $this->objet->detach();
        $this->property->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->objet."->".$this->property;
    }

    function getRegex(){
        return array('property_regex',
        'property_accolade_regex',
                     );
    }

}

?>