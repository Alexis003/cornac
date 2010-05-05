<?php

class rawtext_empty_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CLOSE_TAG);
    }
    
    function check($t) {
      if (!$t->hasNext()) { return false; }

      if ($t->checkToken(T_CLOSE_TAG) &&
          $t->getNext()->checkToken(T_OPEN_TAG)) {
            if ($t->getNext(1)->checkCode('=')) {
                // cas d'un raw text suivi d'un <?= 
                // on attend
                return false; 
            }
              $this->args = array(0);
              $this->remove = array( 1);
          mon_log(get_class($t)." => ".__CLASS__);
          return true;
      }

      return false;
    }
}
?>