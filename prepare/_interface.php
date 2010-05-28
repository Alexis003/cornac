<?php

class _interface extends instruction {
    protected $name = null;
    protected $block = null;
    protected $extends = array();
    
    function __construct($entree = null) {
        parent::__construct(array());
        
        $this->name = $this->toToken_traite($entree[0]);
        unset($entree[0]);
        $this->block = array_pop($entree);
        
        foreach($entree as $e) {
            $this->extends[] = $this->make_token_traite($e);
        }
    }

    function __toString() {
        $retour = __CLASS__." interface {$this->name} ";
        if (count($this->extends) > 0) {
            $retour .= " extends ".join(', ', $this->extends);
        }
        $retour .= "{ ".$this->block." } ";
        
        return $retour;
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
        foreach($this->extends as &$e) {
            $e->detach();
        }
    }

    function getRegex(){
        return array('interface_normal_regex',
                    );
    }

}

?>