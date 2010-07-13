<?php

class inclusion extends instruction {
    protected $inclusion;
    
    function __construct($inclusion) {
        parent::__construct(array());

        $this->inclusion = $inclusion[0];
    }

    function __toString() {
        return __CLASS__." ".$this->inclusion;
    }

    function getInclusion() {
        return $this->inclusion;
    }

    function neutralise() {
       $this->inclusion->detach();
    }

    function getRegex(){
        return array('inclusion_normal_regex',
                     'inclusion_noparenthesis_regex',
                    );
    }
}

?>