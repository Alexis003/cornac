<?php

class _for extends instruction {
    protected $init = null;
    protected $fin = null;
    protected $increment = null;
    protected $block = null;

    function __construct($expression) {
        parent::__construct(array());

        if ($expression[0]->getCode() == ';') {
            $this->init = null;
        } else {
            $this->init = $expression[0];
        }

        // @note immediate processing of block
        $this->block = array_pop($expression);

        if ($expression[1]->checkClass('sequence') && $expression[2]->checkCode(')')) {
            $x = $expression[1]->getElements();
            if (count($x) == 2) { 
                $this->fin = $x[0];
                $this->increment = $x[1];
                
                return;
            } elseif (count($x) == 1) {
                $this->fin = $x[0];
                // @for_translation puis on continue comme d'hab, increment est dans expression[2];
            } else {
                $this->stop_on_error("Wrong number of elements  : '".count($x)."' in ".__METHOD__);
            }
        } elseif ($expression[1]->getCode() == ';') {
            $this->fin = null;
        } else {
            $this->fin = $expression[1];
        }
        
        if ($expression[2]->getCode() == ')') {
            $this->increment = null;
        } else {
            $this->increment = $expression[2];
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