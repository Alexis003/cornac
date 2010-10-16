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

class method_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->hasPrev() && $t->getPrev()->checkCode('->')) { return false; }

        if ($t->checkNotClass(array('variable',
                                    'property',
                                    'tableau',
                                    'method',
                                    'functioncall',
                                    'property_static',
                                    'method_static'))) { return false;}
        if ($t->getNext()->checkNotOperator('->')) { return false; }
        if ($t->getNext(1)->checkNotClass('functioncall')) { return false; }

        $this->args   = array(0, 2);
        $this->remove = array(1, 2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>