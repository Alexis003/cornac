<?php

class declare_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DECLARE);
    }
 
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        
        if ($t->getNext()->checkClass('parentheses') && 
            $t->getNext(1)->checkOperateur(';')) {
            $this->args = array(1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }

        if ($t->getNext()->checkClass('parentheses') && 
            $t->getNext(1)->checkClass('block')) {
            $this->args = array(1, 2);
            $this->remove = array(1, 2);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        if ($t->getNext()->checkOperateur('(') && 
            $t->getNext(1)->checkClass('arginit') &&
            $t->getNext(2)->checkCode(',') &&
            $t->getNext(3)->checkClass('arginit') &&
            $t->getNext(4)->checkOperateur(')') &&
            $t->getNext(5)->checkNotOperateur(':')
            ) {
            
            $this->args = array(2,4);
            $this->remove = array(1,2,3,4,5);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        
        
        return false;
    }
}
?>