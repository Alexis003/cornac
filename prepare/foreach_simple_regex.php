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

class foreach_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOREACH);
    }
    
    function check($t) {
        if (!$t->hasNext(5)) { return false; }

        if ($t->getNext()->checkNotCode('(')) { return false; }
        if ($t->getNext(1)->checkNotClass(_foreach::$incoming_vars)) { return false; }
        if ($t->getNext(2)->checkNotToken(T_AS)) { return false;}
        if ($t->getNext(3)->checkNotClass(_foreach::$blind_values)) { return false; }
        if ($t->getNext(4)->checkNotOperator(')')) { return false; }
        
        if ($t->getNext(5)->checkClass('block')) {
            $this->args = array(2, 4, 6);
            $this->remove = array(1,2,3,4,5,6);
            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
            return true;
        } elseif ($t->getNext(5)->checkForBlock()) {
            if ($t->getNext(6)->checkForAssignation()) { return false; }
            $regex = new modele_regex('block',array(0), array());
            Token::applyRegex($t->getNext(5), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (".__CLASS__.")");
            return false; 
        } elseif ($t->getNext(5)->checkClass(array('variable','_array','property','property_static'))) {
            if ($t->getNext(6)->checkNotCode(';')) { return false; }
            $regex = new modele_regex('block',array(0), array());
            Token::applyRegex($t->getNext(5), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (".__CLASS__.")");
            return false; 
        } elseif ($t->getNext(5)->checkCode(';')) {
            $this->args = array(2, 4, 6);
            $this->remove = array(1,2,3,4,5,6);
            
            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => no block ".__CLASS__);
            return true;
        } else {
            return false;
        }
    }
}

?>