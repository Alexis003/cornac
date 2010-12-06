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

class functioncall_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }

    function check($t) {
        if (!$t->hasNext() ) { return false; }

// @note _nsname
        if ($t->hasPrev() && 
            $t->getPrev()->checkOperator('\\')) { return false; }

        if ($t->hasPrev(2) && 
            $t->getPrev()->checkOperator('&') &&
            $t->getPrev(1)->checkToken(T_FUNCTION)) { return false; }

        if ((!$t->hasPrev() || 
              $t->getPrev()->checkNotToken(T_FUNCTION)) &&
              ($t->checkFunction() || $t->checkToken(array(T_STATIC)) || $t->checkClass(array('_nsname'))) &&
              $t->getNext()->checkClass('arglist')) {

            if ($t->getNext(1)->checkOperator(array('{','(')) ||
                $t->getNext(1)->checkClass('parentheses')) { return false; }

            $this->args = array(0 , 1);
            $this->remove[] = 1;

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        
        return false;
    }
}
?>