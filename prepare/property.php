<?php

class property extends token { 
    private $objet = null;
    private $property = null;

    function __construct($entree) {
        parent::__construct();
        
        if (is_array($entree) && count($entree) == 2) {
            if ($entree[0]->checkClass('Token')) {
                $objet = new token_traite($entree[0]);
                $objet->replace($entree[0]);
            } else {
                $objet = $entree[0];
            }

            $this->objet = $objet;
            
            $this->property = $entree[1];
        } else {
            die("mauvais nombre d'entree\n".__METHOD__);
            $this->property = $entree;
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