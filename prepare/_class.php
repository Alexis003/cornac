<?php

class _class extends instruction {
    protected $_abstract = null;
    protected $nom = array();
    protected $extends = null;
    protected $implements = array();
    protected $block = null;
    

    function __construct($entree) {
        parent::__construct(array());

        $pos = 0;
        if ($entree[$pos]->checkToken(array(T_ABSTRACT, T_FINAL))) {
            $this->_abstract = $this->make_token_traite($entree[$pos]);
            $pos += 1;
        }

        $this->nom = $this->toToken_traite($entree[$pos]);
        $pos ++;
        
        if ($entree[$pos]->checkToken(T_EXTENDS)) {
            $this->extends = $this->toToken_traite($entree[$pos + 1]);
            $pos += 2;
        }

        if ($entree[$pos]->checkToken(T_IMPLEMENTS)) {
            $this->implements[] = $this->toToken_traite($entree[$pos + 1]);
            $pos += 2;
            
            while ($entree[$pos]->checkCode(',')) {
                $this->implements[] = $this->toToken_traite($entree[$pos + 1]);
                $pos += 2;
            }
        }
        $this->block = $entree[$pos];
    }
    
    function __toString() {
        $retour = __CLASS__." class ".$this->nom;
        if (!is_null($this->extends)) {
            $retour .= " extends ".$this->extends;
        }
        if (count($this->implements) > 0) {
            $retour .= " implements ".join(', ', $this->implements);
        }
        $retour .= " {".$this->block."} ";

        return $retour;
    }

    function getNom() {
        return $this->nom;
    }

    function getAbstract() {
        return $this->_abstract;
    }

    function getExtends() {
        return $this->extends;
    }

    function getImplements() {
        return $this->implements;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->nom->detach();
        if (count($this->implements) > 0) {
            foreach($this->implements as $e) {
                $e->detach();
            }
        }
        if (!is_null($this->extends)) {
            $this->extends->detach();
        }
        if (!is_null($this->_abstract)) {
            $this->_abstract->detach();
        }
        $this->block->detach();
    }

    function getRegex() {
        return array( 'class_simple_regex',
                    );
    }
}

?>