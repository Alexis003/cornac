<?php

class _declare extends instruction {
    protected $ticks = null;
    protected $encoding = null;
    protected $block = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        if ($entree[count($entree) - 1]->checkClass('block')) {
            $this->block = array_pop($entree);
        } else {
            // @empty_else
        }
        
        if ($entree[0]->checkClass('parentheses')) {
            // @doc we expect no initialisation 
            if (!$this->set(strtolower($entree[0]->getContenu()->getVariable()->getCode()), 
                            $entree[0]->getContenu()->getValeur())) {
                $this->stop_on_error(($entree[0]->getContenu()->getVariable()." is unknown in ".__METHOD__."\n");
            }
        } elseif ($entree[0]->checkClass('arginit')) {
            // @doc we expect an initialisation 
            if (!$this->set(strtolower($entree[0]->getVariable()->getCode()), 
                            $entree[0]->getValeur())) {
                $this->stop_on_error(($entree[0]->getVariable()." is unknown in ".__METHOD__."\n");
            }
            if (!$this->set(strtolower($entree[1]->getVariable()->getCode()), 
                            $entree[1]->getValeur())) {
                stop_on_error($entree[1]->getVariable()." is unknown in ".__METHOD__."\n");
            }
        } else {
            $this->stop_on_error("Entree is of unexpected class ".get_class($entree[0])." in ".__METHOD__."\n");
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