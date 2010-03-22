<?php

class return_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_RETURN);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkToken(array(T_RETURN)) &&
            $t->getNext()->checkNotClass('Token') &&
            $t->getNext(1)->checkCode(';')
            ) {
              $this->args = array(1);
              $this->remove = array(1);
  
              mon_log(get_class($t)." => ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>