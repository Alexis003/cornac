<?php

class tableau extends variable {
    protected $variable = null;
    protected $index = null;

    function __construct($variable) {
        parent::__construct();
        
        if (is_array($variable)) {
            $this->variable = $variable[0];
            $this->index = $variable[1];
        } else {
            $this->stop_on_error('No way we end up here : '.__METHOD__);
        }
    }
        
    function neutralise() {
        $this->variable->detach();
        $this->index->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->variable."[".$this->index."]";
    }

    function getVariable() {
        return $this->variable;
    }

    function getIndex() {
        return $this->index;
    }

    function getRegex(){
        return array('tableau_regex',
                     'tableau_accolade_regex',
                     );
    }
}

?>