<?php

class arginit_array_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }

    function check($t) {
        if (!$t->hasNext(2) ) { return false; }
        if (!$t->hasPrev() ) { return false; }

        if ($t->getPrev()->checkCode(array('(',',')) &&
            $t->checkClass(array('variable','reference')) &&
            $t->getNext()->checkCode('=') && 
            $t->getNext(1)->checkClass('functioncall') &&  // en fait, on veut juste array... 
            $t->getNext(2)->checkCode(array(',',')'))
            ) {
                $this->args = array(0, 2);
                $this->remove = array(1, 2);

                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            }
        
        return false;
    }
}
?>