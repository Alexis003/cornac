<?php

class _declare extends instruction {
    protected $ticks = null;
    protected $encoding = null;
    protected $block = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        if ($entree[count($entree) - 1]->checkClass('block')) {
            $this->block = array_pop($entree);
        }
        if ($entree[0]->checkClass('parentheses')) {
            // on attend un arginit
            if (!$this->set(strtolower($entree[0]->getContenu()->getVariable()->getCode()), 
                            $entree[0]->getContenu()->getValeur())) {
                die($entree[0]->getContenu()->getVariable()." est inconnue dans ".__METHOD__."\n");
            }
        } elseif ($entree[0]->checkClass('arginit')) {
            if (!$this->set(strtolower($entree[0]->getVariable()->getCode()), 
                            $entree[0]->getValeur())) {
                die($entree[0]->getVariable()." est inconnue dans ".__METHOD__."\n");
            }
            if (!$this->set(strtolower($entree[1]->getVariable()->getCode()), 
                            $entree[1]->getValeur())) {
                die($entree[1]->getVariable()." est inconnue dans ".__METHOD__."\n");
            }
        }
    }
    
    function set($nom, $valeur) {
        if (in_array($nom, array('ticks','encoding'))) {
            $this->$nom = $valeur;
            return true;
        }
        return false;
    }

    function __toString() {
        $x = __CLASS__." ticks= ".$this->tick." encoding = ".$this->encoding;
        if (!is_null($this->block)) { $x .= " ".$this->block;}
        return $x; 
    }

    function getTicks() {
        return $this->ticks;
    }

    function getEncoding() {
        return $this->encoding;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        if (!is_null($this->ticks)) {
            $this->ticks->detach();
        }
        if (!is_null($this->encoding)) {
            $this->encoding->detach();
        }
        if (!is_null($this->block)) {
            $this->block->detach();
        }
    }

    function getRegex(){
        return array('declare_normal_regex',
                     'declare_alternative_regex',
                    );
    }

}

?>