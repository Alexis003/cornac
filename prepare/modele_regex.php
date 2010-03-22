<?php

class modele_regex extends analyseur_regex {

    function __construct($class, $args, $remove) {
        parent::__construct(array());
        
        $this->class = $class;
        $this->args = $args;
        $this->remove = $remove;
    }

    function getTokens() {
        return array();
    }
    
    function check($t) {
        mon_log(get_class($t)." => Modele ({$this->class}) ");
        return true;
    }
}
?>