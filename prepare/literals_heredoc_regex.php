<?php

class literals_heredoc_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_START_HEREDOC);
    }
    
    function check($t) {
            
        if ($t->getNext()->checkToken(T_ENCAPSED_AND_WHITESPACE) &&
            $t->getNext(1)->checkToken(T_END_HEREDOC)) {
              $this->args = array(1);
              $this->remove = array(1,2);

              mon_log(get_class($t)." => ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>