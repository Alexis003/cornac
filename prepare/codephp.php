<?php

class codephp extends instruction {
    private $php_code = array();
    
    function __construct($expression = null) {
        parent::__construct(array());

        if (is_null($expression) || empty($expression)) {
            $this->php_code = new sequence(array());
        } else {
            $this->php_code =  $expression[0];
            if (count($expression) > 1) {
                $this->stop_on_error("We lost some elements in ".__METHOD__);
            }
        }
    }

    function neutralise() {
        $this->php_code->detach();
    }

    function getphp_code() {
        return $this->php_code;
    }
    
    function getRegex(){
        return array('codephp_empty_regex', 
                     'codephp_unfinished_regex', 
                     'codephp_unfinishedempty_regex', 
                     'codephp_normal_regex',
                     'codephp_avecpointvirgule_regex',
                     'codephp_unfinishedavecpointvirgule_regex',
                    );
    }
}

?>