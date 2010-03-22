<?php

class cdtternaire extends instruction {
    function __construct($entree) {
        parent::__construct(array());
        if (!is_array($entree)) {
        
        } elseif (count($entree) == 3) {
            if ($entree[0]->checkClass('arglist')) {
                $this->condition = $entree[0]->getList();
                $this->condition = $this->condition[0];
            } else {
                $this->condition = $entree[0];
            }
            $this->vraie     = $entree[1];
            $this->faux      = $entree[2];
        } else {
            die("Mauvais nombre d'entree dans ".__CLASS__);
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    static function getRegex() {
        return array('cdtternaire_normal_regex');
    }

    function getCondition() {
        return $this->condition;
    }

    function getVraie() {
        return $this->vraie;
    }

    function getFaux() {
        return $this->faux;
    }

    function neutralise() {
        $this->condition->detach();
        $this->vraie->detach();
        $this->faux->detach();
    }
}
?>