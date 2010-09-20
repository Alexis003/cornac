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

class foreach_alternative_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOREACH);
    }
    
    function check($t) {
        if (!$t->hasNext(6)) { return false; }

        if ($t->checkToken(array(T_FOREACH)) &&
            $t->getNext()->checkCode('(')    &&
            $t->getNext(1)->checkClass(_foreach::$incoming_vars)  &&
            $t->getNext(2)->checkToken(T_AS)) {
            $posi = 3;
            
            if ($t->getNext(3)->checkClass(array('variable','tableau','property','reference'))  &&
                $t->getNext(4)->checkToken(T_DOUBLE_ARROW)) {
                $posi = 5;    
            }

            if ( $t->getNext($posi)->checkNotClass(array('variable','tableau','property','reference'))  ||
                 $t->getNext($posi + 1)->checkNotCode(')')) {
                 return false;
            } 
                $posi += 2;
                if ($t->getNext($posi)->checkNotCode(':')) { return false; }

                $args = array();
                $remove = array(-1);
                $pos = 0;
                $var = $t->getNext($posi + 1);
                
                while($var->checkNotToken(T_ENDFOREACH)) {
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

                    if ($var->checkToken(T_FOREACH) ) {
                        // Un autre if qui démarre? On aime pas les imbrications
                        return false;
                    }
                    
                    if ($var->checkCode(';') ) {
                        // un point-virgule qui traine. Bah....
                        $remove[] = $pos;
                        $pos++;
                        $var = $var->getNext();
                        continue;
                    }
        
                    // pas traitable ? On annule tout.
                    $this->args = array();
                    $this->remove = array();
                    return false;
                }
                
                $remove[] = $pos;

                $regex = new modele_regex('block',$args, $remove);
                Token::applyRegex($t->getNext($posi+1), 'block', $regex);

                mon_log(get_class($t)." => block (".__CLASS__.")");
                return false; 
        } else {
            return false;
        }
    }
}

?>