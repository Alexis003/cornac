<?php

class for_sequence_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOR);
    }
    
    function check($t) {
        if (!$t->hasNext(5)) { return false; }

        if ($t->checkNotToken(array(T_FOR))) { return false;}
        if ($t->getNext()->checkNotCode('(')) { return false;}
        
        if ($t->getNext(1)->checkNotClass('Token')  &&
            $t->getNext(2)->checkCode(";")          &&
            $t->getNext(3)->checkClass('sequence')  &&
            $t->getNext(4)->checkCode(")")          &&
            $t->getNext(5)->checkForBlock(true)
            
            ) {
            
              $this->args = array(2, 4, 6);
              $this->remove = array(1,2,3,4,5,6);
  
              mon_log(get_class($t)." => (Token; sequence) ".__CLASS__);
              return true;
        } elseif ($t->getNext(1)->checkNotClass('Token')  &&
                  $t->getNext(2)->checkCode(";")          &&
                  $t->getNext(3)->checkClass('sequence')  &&
                  $t->getNext(4)->checkNotClass('Token')  &&
                  $t->getNext(5)->checkCode(")")          &&
                  $t->getNext(6)->checkForBlock(true)
 
            
            ) {
            
              $this->args = array(2, 4, 5, 7);
              $this->remove = array(1,2,3,4,5,6,7);
  
              mon_log(get_class($t)." => (Token; sequence Token) ".__CLASS__);
              return true;
        } elseif ($t->getNext(1)->checkCode(";")          &&
                  $t->getNext(2)->checkClass('sequence')  &&
                  $t->getNext(3)->checkNotClass('Token')  &&
                  $t->getNext(4)->checkCode(")")          &&
                  $t->getNext(5)->checkForBlock(true)
            
            ) {
            
              $this->args = array(2, 3, 4, 6);
              $this->remove = array(1,2,3,4,5,6,7);
  
              mon_log(get_class($t)." => (;sequence Token) ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>