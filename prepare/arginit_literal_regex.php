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

class arginit_literal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }

    function check($t) {
        if (!$t->hasNext(3) ) { return false; }
        if (!$t->hasPrev() ) { return false; }
        
        if ($t->getPrev()->checkNotCode(array('(',',')) &&
            $t->getPrev()->checkNotToken(array(T_VAR, T_PROTECTED, T_PRIVATE, T_PUBLIC))) { return false; }
        
        if ($t->checkNotClass(array('variable','constante','reference'))) { return false; }
        if ($t->getNext()->checkNotCode('=')) { return false; }
        if ($t->getNext(1)->checkNotClass(array('constante','literals','signe'))) { return false; }
        if ($t->getPrev(1)->checkToken(array(T_FOR,T_IF, T_ELSEIF))) { return false; }
        if ($t->getNext(2)->checkNotCode(array(',',')')))  { return false;}

        $this->args = array(0, 2);
        $this->remove = array(1, 2);
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>