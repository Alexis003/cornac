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

class static_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_STATIC);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->hasPrev() && $t->getPrev()->checkToken(array(T_PROTECTED, T_PUBLIC, T_PRIVATE))) { return false; }
        
        if ($t->getNext()->checkNotClass(array('variable','affectation'))) { return false; }
        if ($t->getNext(1)->checkOperator('=')) { return false; }

        $this->args = array(1);
        $this->remove = array(1);
        
        if ($t->getNext(1)->checkCode(';')) {
            $this->remove[] = 2;
        } elseif ($t->getNext(1)->checkBeginInstruction()) {
        // @note may be a new instruction (even a sequence)
            // @note OK, but do nothing
        } elseif ($t->getNext(1)->checkToken(T_CLOSE_TAG)) {
            // @note OK, but do nothing
        } else {
            return false;
        }

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>