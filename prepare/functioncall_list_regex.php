<?php

class functioncall_list_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_LIST);
    }

    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotToken(T_LIST)) { return false; }
        if ($t->getNext()->checkNotCode('(')) { return false; }
        if ($t->getNext()->checkClass('arglist')) { return false; }

        $args = array();
        $remove = array(0);
        
        $var = $t->getNext(1);
        $pos = 1;
        
        while($var->checkNotCode(')')) {
            if ($var->checkClass(array('variable','tableau','property','property_static'))) {
                $args[] = $pos;
                $remove[] = $pos;                
                
                $pos += 1;
                $var = $var->getNext();
                if (is_null($var)) {
                    return false;
                }
                continue;
            }

            if ($var->checkCode(',')) {
                if ($var->getPrev()->checkCode(array(',','('))) {
                    $args[] = $pos;
                }
                $remove[] = $pos;                
                
                $pos += 1;
                $var = $var->getNext();
                if (is_null($var)) {
                    return false;
                }
                continue;
            }
            
            return false;
        }
        $remove[] = $pos;
        
        $regex = new modele_regex('arglist',$args, $remove);
        Token::applyRegex($t->getNext(), 'arglist', $regex);
        
        mon_log(get_class($t)." => ".__CLASS__);
        return false; 
    }
}
?>