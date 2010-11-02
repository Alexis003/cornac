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

class clevaleur_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DOUBLE_ARROW);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if (!$t->hasPrev()) { return false; }

        if ($t->getNext()->checkClass('Token')) { return false; }
        if ($t->getPrev()->checkClass(array('Token', 'arglist'))) { return false; }
        if ($t->getPrev(1)->checkToken(T_AS)) { return false; }
        if ($t->getPrev(1)->checkOperator(array('->','::'))) { return false; } 
        if ($t->getNext(1)->checkOperator(array('->','::'))) { return false; } 

        if ($t->getNext(1)->checkCode(array('[','->','++','--','=','.=','*=','+=','-=','/=','%=',
                                                 '>>=','&=','^=','>>>=', '|=','<<=','>>=','?','(','{'))) { return false; }
        if ($t->getNext(1)->checkClass(array('arglist','parentheses'))) { return false; }

        $this->args = array(-1, 1);
        $this->remove = array(-1, 1);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>