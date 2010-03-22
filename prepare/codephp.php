<?php

class codephp extends instruction {
    
    function __construct($php_code = null) {
        parent::__construct(array());

        if (is_null($php_code)) {
            $this->php_code = new sequence(array());
        } else {
            $this->php_code = $php_code[0];
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