<?php

class rawtext_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_INLINE_HTML);
    }
    
    function check($t) {
        if ($t->checkToken(T_INLINE_HTML)) {
              $this->args = array(0);
              $this->remove = array();
              
              if ($t->hasPrev() && $t->hasNext()) {
                if ($t->getPrev()->checkToken(T_CLOSE_TAG) &&
                    $t->getNext()->checkToken(T_OPEN_TAG)) {
                      $this->args = array(0);
                      $this->remove = array(-1, 1);
                    }
              }
  
              mon_log(get_class($t)." => ".__CLASS__);
              return true;
            } else {
                return false;
            }

    }
}
?>