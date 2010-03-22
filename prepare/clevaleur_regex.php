<?php

class clevaleur_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DOUBLE_ARROW);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if (!$t->hasPrev()) { return false; }

        if ($t->checkNotToken(T_DOUBLE_ARROW)) {return false; }

        if ($t->getNext()->checkNotClass('Token') && 
            $t->getPrev()->checkNotClass(array('Token', 'arglist')) &&
            ($t->getPrev(1)->checkNotToken(T_AS)) &&
             $t->getPrev(1)->checkNotOperateur(array('->','::')) &&
             $t->getNext(1)->checkNotCode(array('[','->','++','--','=','.=','*=','+=','-=','/=','%=',
                                                 '>>=','&=','^=','>>>=', '|=','<<=','>>=','?','(','{')) &&
             $t->getNext(1)->checkNotClass(array('arglist','parentheses')) 
            ) {
            
            $this->args = array(-1, 1);
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>