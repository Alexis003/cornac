<?php

class _return extends instruction {
    protected $retour = null;

    function __construct($entree = null) {
        parent::__construct(array());

        if (isset($entree[0])) {
            $this->retour = $entree[0];
        }
    }
    
    function __toString() {
        return __CLASS__." return ".$this->retour;
    }

    function getRetour() {
        return $this->retour;
    }

    function neutralise() {
        if (!is_null($this->retour)) {
            $this->retour->detach();
        }
    }

    function getRegex() {
        return array(
        'return_simple_regex',
        'return_empty_regex',
);
    }
}

?>