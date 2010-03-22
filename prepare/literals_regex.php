<?php

class literals_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_LNUMBER, 
                                 T_CONSTANT_ENCAPSED_STRING, 
                                 T_ENCAPSED_AND_WHITESPACE, 
                                 T_NUM_STRING,
                                 T_DNUMBER);
    }
    
    function check($t) {
            
        if ($t->checkToken(array(T_LNUMBER, 
                                 T_CONSTANT_ENCAPSED_STRING, 
                                 T_ENCAPSED_AND_WHITESPACE, 
                                 T_NUM_STRING,
                                 T_DNUMBER))) {
              $this->args = array(0);
              $this->remove = array();

              mon_log(get_class($t)." => ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>