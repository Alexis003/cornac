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

class label_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkNotToken(T_STRING)) { return false; }
        if ($t->getNext()->checkNotOperator(':')) { return false; }
        if ($t->getPrev()->checkToken(array(T_CASE, T_INSTANCEOF))) { return false; }
        if ($t->getPrev()->checkOperator(array('?','->','::','.'))) { return false; }
        if ($t->getPrev()->checkForComparison()) { return false; }
        if ($t->getPrev()->checkForLogical()) { return false; }

        $this->args = array(0);
        $this->remove = array(0, 1);

        mon_log(get_class($t)." => label  (".__CLASS__.")");
        return true;
    }
}
?>