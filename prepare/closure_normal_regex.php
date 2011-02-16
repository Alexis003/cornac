<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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

class closure_normal_regex extends Cornac_Tokenizeur_Regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FUNCTION);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if ($t->getNext()->checkOperator(array('(')) && 
            $t->getNext(1)->checkOperator(array(')'))) { 
            $pos = 2;

            $this->args = array();
            $this->remove = array(1,2);
        } elseif ($t->getNext()->checkClass('arglist')) {
            $pos = 1;

            $this->args = array(1);
            $this->remove = array(1);
        } else {
            return false;
        }

        if ($t->getNext($pos)->checkToken(T_USE) &&
            $t->getNext($pos + 1)->checkClass('arglist')) {
                
            $this->args[] = $pos + 2;
            $this->remove[] = $pos + 1;
            $this->remove[] = $pos + 2;
            
            $pos += 2;
        }

        if ($t->getNext($pos)->checkNotClass(array('block'))) { return false; }
        $this->args[] = $pos + 1;
        $this->remove[] = $pos + 1;

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".$this->getTname());
        return true; 
    }
}
?>