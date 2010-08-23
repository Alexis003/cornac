<?php

class _for extends instruction {
    protected $init = null;
    protected $fin = null;
    protected $increment = null;
    protected $block = null;

    function __construct($entree) {
        parent::__construct(array());

        if ($entree[0]->getCode() == ';') {
            $this->init = null;
        } else {
            $this->init = $entree[0];
        }

        // @note immediate processing of block
        $this->block = array_pop($entree);

        if ($entree[1]->checkClass('sequence') && $entree[2]->checkCode(')')) {
            $x = $entree[1]->getElements();
            if (count($x) == 2) { 
                $this->fin = $x[0];
                $this->increment = $x[1];
                
                return;
            } elseif (count($x) == 1) {
                $this->fin = $x[0];
                // @for_translation puis on continue comme d'hab, increment est dans entree[2];
            } else {
                $this->stop_on_error("Wrong number of elements  : '".count($x)."' in ".__METHOD__);
            }
        } elseif ($entree[1]->getCode() == ';') {
            $this->fin = null;
        } else {
            $this->fin = $entree[1];
        }
        
        if ($entree[2]->getCode() == ')') {
            $this->increment = null;
        } else {
            $this->increment = $entree[2];
        }
        
    }
    
    function __toString() {
        return __CLASS__." for (".$this->init."; ".$this->fin."; ".$this->increment." ) {".$this->block."} ";
    }

    function getInit() {
        return $this->init;
    }

    function getFin() {
        return $this->fin;
    }

    function getIncrement() {
        return $this->increment;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        if (!is_null($this->init)) {
            $this->init->detach();
        }
        if (!is_null($this->fin)) {
            $this->fin->detach();
        }
        if (!is_null($this->increment)) {
            $this->increment->detach();
        }
        $this->block->detach();
    }

    function getRegex() {
        return array(
    'for_simple_regex',
    'for_sequence_regex',
    'for_comma1_regex',
    'for_comma2_regex',
    'for_comma3_regex',
    
    'for_alternative_regex',
);
    }
}

?>