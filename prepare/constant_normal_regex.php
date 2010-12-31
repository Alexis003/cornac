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

class constant_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_STRING,Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if (!$t->hasPrev()) { return false; }
        
        if ($t->checkNotClass('Token')) { return false; } 
        if ($t->checkNotToken(array(T_STRING, T_DIR, T_FILE, T_FUNC_C, T_LINE, T_METHOD_C, T_NS_C, T_CLASS_C))) { return false; }
        if ($t->getNext()->checkCode(array('(','::','{', '\\'))) { return false; }
        if ($t->getNext()->checkCode(array(':')) &&
            $t->getPrev()->checkNotOperator(array('::'))) { return false; }
        if ($t->getNext()->checkToken(array(T_VARIABLE, T_AS))) { return false; }
        if ($t->getNext()->checkClass(array('variable','affectation'))) { return false; }

        if ($t->getPrev()->checkCode(array('->','\\'))) { return false; }
        if ($t->getPrev()->checkToken(array(T_CLASS, 
                                            T_EXTENDS, 
                                            T_IMPLEMENTS, 
                                            T_NAMESPACE, 
                                            T_USE,
                                            T_AS,
                                            T_GOTO))) { return false; }

        if ($t->getPrev()->checkToken(array(T_OPEN_TAG)) && 
            $t->getPrev()->checkCode('<?')) { return false; }

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>