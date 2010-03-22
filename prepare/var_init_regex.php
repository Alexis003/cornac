<?php

class var_init_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC);
    }
    
    function check($t) {
        return false;
        if (!$t->hasNext(2)) { return false; }

        if ($t->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC)) &&
            $t->getNext()->checkClass(array('affectation','arginit'))    &&
            $t->getNext(1)->checkCode(';')   
            ) {
              $this->args = array(1);
              $this->remove = array(1);

                if ($t->hasPrev() &&
                    $t->getPrev()->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC))) {

                    $this->args[] = -1;
                    $this->remove[] = -1;

                    sort($this->args);
                    sort($this->remove);
                }

  
              mon_log(get_class($t)." => ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>