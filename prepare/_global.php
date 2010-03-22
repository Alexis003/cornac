<?php

class _global extends instruction {
    protected $variables = array();

    function __construct($entree) {
        parent::__construct(array());

        $this->variables = $entree;
    }
    
    function __toString() {
        return __CLASS__." ".join(', ', $this->variables);
    }

    function getVariables() {
        return $this->variables;
    }

    function neutralise() {
        foreach($this->variables as $v) {
            $v->detach();
        }
    }

    function getRegex() {
        return array(
    'global_simple_regex',
);
    }
}

?>