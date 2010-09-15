<?php

class _interface extends instruction {
    protected $name = null;
    protected $block = null;
    protected $extends = array();
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->name = $this->toToken_traite($expression[0]);
        unset($expression[0]);
        $this->block = array_pop($expression);
        
        foreach($expression as $e) {
            $this->extends[] = $this->make_token_traite($e);
        }
    }

    function __toString() {
        $return = __CLASS__." interface {$this->name} ";
        if (count($this->extends) > 0) {
            $return .= " extends ".join(', ', $this->extends);
        }
        $return .= "{ ".$this->block." } ";
        
        return $return;
    }

    function getBlock() {
        return $this->block;
    }

    function getName() {
        return $this->name;
    }

    function getExtends() {
        return $this->extends;
    }

    function neutralise() {
        $this->block->detach();
        $this->name->detach();
        foreach($this->extends as $e) {
            $e->detach();
        }
    }

    function getRegex(){
        return array('interface_normal_regex',
                    );
    }

}

?>