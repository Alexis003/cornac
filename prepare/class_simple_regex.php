<?php

class class_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CLASS);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }


        if ($t->getNext()->checkToken(T_STRING)) {
              $this->args = array(1);
              $this->remove = array(1);
              $pos = 1;
              $var = $t->getNext(1);
              
              if ($var->checkToken(T_EXTENDS)) {
                  $pos++;
                  $this->args[]   = $pos;
                  $this->remove[] = $pos;

                  $pos++;
                  $this->args[]   = $pos;
                  $this->remove[] = $pos;

                  $var = $var->getNext(1);
              }
              
              if ($var->checkToken(T_IMPLEMENTS)) {
                  $pos++;
                  $this->args[]   = $pos;
                  $this->remove[] = $pos;

                  $pos++;
                  $this->args[]   = $pos;
                  $this->remove[] = $pos;

                  $var = $var->getNext(1);
                  
                  while($var->checkCode(',')) {
                      $pos++;
                      $this->args[]   = $pos;
                      $this->remove[] = $pos;

                      $pos++;
                      $this->args[]   = $pos;
                      $this->remove[] = $pos;

                      $var = $var->getNext(1);
                  }
              }
              
              if ($var->checkClass('block')) {
                  $pos++;
                  $this->args[]   = $pos;
                  $this->remove[] = $pos;
                  
                  if ($t->hasPrev() && $t->getPrev()->checkToken(array(T_ABSTRACT, T_FINAL))) {
                      $this->args[] = -1;
                      $this->remove[] = -1;
                      
                      sort($this->args);
                      sort($this->remove);
                  }

                  mon_log(get_class($t)." => ".__CLASS__);
                  return true;
              }
              
              // on a échoué à comprendre. On annule tout
              $this->args = array();
              $this->remove = array();
              return false;
        } else {
            return false;
        }
    }
}

?>