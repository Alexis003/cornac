<?php

class _catch extends instruction {
    protected $exception = null;
    protected $variable = null;
    protected $block = null;
    
    function __construct($entree = null) {
        parent::__construct(array());
        
        $this->block = array_pop($entree);
        if (count($entree) == 2) {
            $this->exception = $this->make_token_traite($entree[0]);
            $this->variable  = $entree[1];
        } else {
            die(__CLASS__." n'a pas recu le bon nombre d'arguments (".count($entree)." au lieu de 3)\n");
        }
    }

    function __toString() {
        return __CLASS__." (".$this->exception." ".$this->variable.") ";
    }

    function getException() {
        return $this->exception;
    }

    function getVariable() {
        return $this->variable;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->exception->detach();
        $this->variable->detach();
        $this->block->detach();
    }

    function getRegex(){
        return array('catch_normal_regex',
                    );
    }

}

?>