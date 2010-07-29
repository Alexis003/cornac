<?php

class _function extends instruction {
    protected $name = '';
    protected $_abstract = null;
    protected $_static = null;
    protected $_visibility = null;
    protected $reference = null;
    protected $args = null;
    protected $block = null;
    

    function __construct($entree) {
        parent::__construct(array());
        
        while($entree[0]->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL))) {

            if ($entree[0]->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE))) {
                $this->_visibility = $this->make_token_traite($entree[0]);
                unset($entree[0]);
                $entree = array_values($entree);
                continue;
            }

            if ($entree[0]->checkToken(array(T_STATIC))) {
                $this->_static = $this->make_token_traite($entree[0]);

                unset($entree[0]);
                $entree = array_values($entree);
                continue;
            }

            if ($entree[0]->checkToken(array(T_ABSTRACT, T_FINAL))) {
                $this->_abstract = $this->make_token_traite($entree[0]);

                unset($entree[0]);
                $entree = array_values($entree);
                continue;
            }
            
            $this->stop_on_error("On ne devrait pas arriver ici : ".__CLASS__);
        }

        if (count($entree) == 3) {
            $this->name = $entree[0];
            $this->args = $entree[1];
            $this->block = $entree[2];
        } elseif (count($entree) == 4) {
            $this->reference = $entree[0];
            $this->name = $entree[1];
            $this->args = $entree[2];
            $this->block = $entree[3];
        } else {
            $this->stop_on_error("Wrong number of arguments  : '".count($entree)."' in ".__METHOD__);
        }
        
        if ($this->block->getCode() == ';') {
            $this->block     = new block();
        }
    }
    
    function __toString() {
        return __CLASS__." function ".$this->name." (".$this->args.") {".$this->block."} ";
    }

    function getName() {
        return $this->name;
    }

    function getReference() {
        return $this->reference;
    }

    function getArgs() {
        return $this->args;
    }

    function getBlock() {
        return $this->block;
    }

    function getVisibility() {
        return $this->_visibility;
    }

    function getAbstract() {
        return $this->_abstract;
    }

    function getStatic() {
        return $this->_static;
    }

    function neutralise() {
        $this->name->detach();
        if (!is_null($this->reference)) {   
            $this->reference->detach();
        }
        if (!is_null($this->_visibility)) {   
            $this->_visibility->detach();
        }
        if (!is_null($this->_static)) {   
            $this->_static->detach();
        }
        if (!is_null($this->_abstract)) {   
            $this->_abstract->detach();
        }
        $this->args->detach();
        $this->block->detach();
    }

    function getRegex() {
        return array(
    'function_simple_regex',
    'function_reference_regex',
    'function_abstract_regex',
    'function_typehint_regex',
    'function_typehintreference_regex',
);
    }
}

?>