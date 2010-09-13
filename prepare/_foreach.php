<?php

class _foreach extends instruction {
    protected $tableau = array();
    protected $key = null;
    protected $value = null;
    protected $block = null;
    
    static $incoming_vars = array('variable','tableau','property', 'property_static',
                                  'functioncall','method','cast','method_static','_new',
                                  'affectation','cdtternaire','parentheses','noscream');

    static $blind_values = array('variable','tableau','property','reference','parentheses','property_static');
    static $blind_keys = array('variable','tableau','property','reference','parentheses','property_static');

    function __construct($expression) {
        parent::__construct(array());
            
        $block = array_pop($expression);
        if ($block->checkCode(';')) {
            $real = new block(array());
            $real->replace($block);
            
            $expression[] = $real;
            $expression = array_values($expression);
        } else {
            $expression[] = $block;
            $expression = array_values($expression);
        }
        
        if (count($expression) == 4) {
            $this->tableau = $expression[0];
            $this->key = $expression[1];
            $this->value = $expression[2];
            $this->block = $expression[3];
        } else {
            $this->tableau = $expression[0];
            $this->key =  null;
            $this->value = $expression[1];
            $this->block = $expression[2];
        }    
    }
    
    function __toString() {
        return __CLASS__." foreach (".$this->tableau.") { ".$this->block." } ";
    }

    function getTableau() {
        return $this->tableau;
    }

    function getKey() {
        return $this->key;
    }

    function getValue() {
        return $this->value;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->tableau->detach();
        if (!is_null($this->key)) { 
            $this->key->detach();
        }
        $this->value->detach();
        $this->block->detach();
    }

    function getRegex() {
        return array(
    'foreach_simple_regex',
    'foreach_withkey_regex',

    'foreach_alternative_regex',
);
    }
}

?>