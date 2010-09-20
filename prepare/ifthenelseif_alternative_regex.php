<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class ifthenelseif_alternative_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF,T_ELSEIF);
    }
    
    function check($t) {
        if (!$t->hasNext(2) ) { return false; }

        if (!$t->checkToken(array(T_IF,T_ELSEIF))) { return false;} 
        if ($t->getNext()->checkNotClass('parentheses')) { return false; }
        if ($t->getNext(1)->checkNotCode(':')) { return false; } 

        $args = array();
        $remove = array(-1);
        $var = $t->getNext(2);            
        $pos = 0;

        while($var->checkNotToken(array(T_ENDIF,T_ELSEIF, T_ELSE))) {
            if ($var->checkToken(T_IF) ) {
                // Un autre if qui démarre? On aime pas les imbrications
                return false;
            }

            if ($var->checkForBlock()) {
                $args[] = $pos;
                $remove[] = $pos;
                if (!$var->hasNext()) { return false; }
                $var = $var->getNext();
                $pos++;
                continue;
            }

            if ($var->checkNotClass(array('block','Token')) && 
                $var->getNext()->checkCode(';')) {
                $args[] = $pos;

                $remove[] = $pos;
                $remove[] = $pos + 1;
                if (!$var->hasNext(1)) { return false; }
                $var = $var->getNext(1);
                $pos += 2;
                continue;
            }

            if ($var->checkCode(';') ) {
                // un point-virgule qui traine. Bah....
                $remove[] = $pos;
                $pos++;
                $var = $var->getNext();
                continue;
            }
            
            // pas traitable ? On annule tout.
            return false;
        }
        
        if ($var->checkToken(T_ENDIF)) {
            $remove[] = $pos;
        }
        
        $regex = new modele_regex('block',$args, $remove);
        Token::applyRegex($t->getNext(2), 'block', $regex);

        mon_log(get_class($t)." => block (".__CLASS__.")");
        return false; 
    }
}
?>