<?php

class analyseur_regex {
    protected $args = array();
    protected $remove = array();
    
    static public $regex = array();

    function __construct($expression) {
        
    }
    
    function getArgs( ) {
        return $this->args;
    }

    function getRemove( ) {
        return $this->remove;
    }

    function reset() {
        $this->args = array();
        $this->remove = array();
    }
    
    function getTokens() {
        return false;
    }

}

?>
