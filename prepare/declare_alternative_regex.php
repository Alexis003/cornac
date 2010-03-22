<?php

class declare_alternative_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DECLARE);
    }
 
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        
        if ($t->getNext()->checkClass('parentheses') && 
            $t->getNext(1)->checkOperateur(':')) {
            $this->args = array(1, 3);
            $this->remove = array(1, 2, 3);

            $var = $t->getNext(2);
            $init = $var;
            
            $args = array();
            $remove = array(-1);
            $pos = 0;

        } elseif ($t->getNext()->checkOperateur('(') && 
            $t->getNext(1)->checkClass('arginit') &&
            $t->getNext(2)->checkOperateur(',') &&
            $t->getNext(3)->checkClass('arginit') &&
            $t->getNext(4)->checkOperateur(')') && 
            $t->getNext(5)->checkOperateur(':') 
            ) {            
            $this->args = array(2,4, 6);
            $this->remove = array(1,2,3,4,5, 6);

            $var = $t->getNext(6);
            $init = $var;
            
            $args = array();
            $remove = array(-1);
            $pos = 0;
        } else {
            return false;
        }
        

         while($var->checkNotToken(T_ENDDECLARE)) {
            if ($var->checkForBlock()) {
                $args[] = $pos;
                $remove[] = $pos;
                if (!$var->hasNext()) { return $t; }
                $var = $var->getNext();
                $pos++;
                continue;
            }

            if ($var->checkNotClass(array('block','Token')) && 
                $var->getNext()->checkCode(';')) {
                $args[] = $pos;

                $remove[] = $pos;
                $remove[] = $pos + 1;
                if (!$var->hasNext(1)) { return $t; }
                $var = $var->getNext(1);
                $pos += 2;
                continue;
            }

            if ($var->checkOperateur(';') ) {
                // un point-virgule qui traine. Bah....
                $remove[] = $pos;
                $pos++;
                $var = $var->getNext();
                continue;
            }

            // pas traitable ? On annule tout.
            return false;
         }

        $remove[] = $pos;

        $regex = new modele_regex('block',$args, $remove);
        Token::applyRegex($init, 'block', $regex);
        
        $this->args[] = 

        mon_log(get_class($t)." => block (".__CLASS__.")");
        return true;
    }
}
?>