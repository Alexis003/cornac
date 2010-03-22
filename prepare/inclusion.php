<?php

class inclusion extends instruction {
    protected $inclusion;

    protected static $tests = array(10 => array(  
                                    ),
                                    11 => array(  
                                    ),
                           );

    protected static $creation = array(//'args' => array(-1, 1),
                                       'remove' => array(
                                            10 => array(1),
                                            11 => array(1,2,3)
                                                         )
                                       );
    
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