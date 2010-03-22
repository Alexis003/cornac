<?php

class switch_alternative_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_SWITCH);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if ($t->getNext()->checkNotClass('parentheses')) { return false; }
        if ($t->getNext(1)->checkNotOperateur(':')) { return false; }

        $pos = 0;
        $var = $t->getNext(2);
        
        $args = array();
        
        while($var->checkNotToken(T_ENDSWITCH)) {
            
            if ($var->checkNotClass(array('_case','_default'))) { return false; }
            
            $args[] = $pos;
            $pos++;
            
            $var = $var->getNext();
        }
        
        $regex = new modele_regex('block',$args, $args);
        Token::applyRegex($t->getNext(2), 'block', $regex);

        $this->args = array(1, 3);
        $this->remove = array(1, 2, 3, 4);
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>