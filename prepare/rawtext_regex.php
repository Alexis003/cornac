<?php

class rawtext_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_INLINE_HTML);
    }
    
    function check($t) {
        $this->args = array(0);
        $this->remove = array();
      
        if ($t->hasPrev() && $t->hasNext()) {
            if ($t->getPrev()->checkToken(T_CLOSE_TAG) &&
                $t->getNext()->checkToken(T_OPEN_TAG)) {
                
                if ($t->getNext(1)->checkCode('=')) {
                    // cas des short tags
                    return false;
                }
                $this->args = array(0);
                $this->remove = array(-1, 1);
            } elseif ($t->getPrev()->checkClass('codephp') &&
                      $t->getNext()->checkToken(T_OPEN_TAG)) {
                
                if ($t->getNext(1)->checkCode('=')) {
                    // cas des short tags
                    return false;
                } 
                $this->args = array(0);
                $this->remove = array(1);

            } elseif ($t->getPrev()->checkToken(T_CLOSE_TAG) &&
                      $t->getNext()->checkClass('codephp')) {
                $this->args = array(0);
                $this->remove = array(-1);
            } elseif ($t->getPrev()->checkClass('codephp') &&
                      $t->getNext()->checkClass('codephp')) {
                      // rien, on peut continuer
            } else {
            // rien du tout...
            }
        }

        mon_log(get_class($t)." => ".__CLASS__);
        return true;
    }
}
?>