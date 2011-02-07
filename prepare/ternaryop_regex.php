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

class ternaryop_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('?');
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(3)) { return false; }

        if ($t->hasPrev(1)) {
            if ($t->getPrev(1)->checkOperator(array('::','->','@'))) { return false; }
            if ($t->getPrev(1)->checkForComparison()) { return false; }
        }

// @note case of the ? <something>  :
        if ($t->getPrev()->checkNotClass(array('Token','arglist')) &&
            $t->getNext()->checkNotClass('Token') &&
            $t->getNext(1)->checkOperator(':') &&
            $t->getNext(2)->checkNotClass('Token') &&
            $t->getNext(3)->checkNotCode(array('->','[','(','::')) &&
           !$t->getNext(3)->checkForAssignation()
            ) {
                $this->args = array(-1, 1, 3);
                $this->remove = array( -1, 1, 2, 3);
    
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ? : ".__CLASS__);
                return true;
            } 

// @note case of the ?:
        if ($t->getPrev()->checkNotClass(array('Token','arglist')) &&
            $t->getNext()->checkOperator(':') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkNotOperator(array('->','[','(','::')) &&
            $t->getNext(2)->checkNotClass(array('arglist')) &&
           !$t->getNext(2)->checkForAssignation()
            ) {
                $regex = new modele_regex('block',array(), array());
                Token::applyRegex($t->getNext(), 'block', $regex);

                $this->args = array(-1, 1, 2);
                $this->remove = array( -1, 1, 2);
    
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ?: ".__CLASS__);
                return true;
            } 
            
            return false;
    }
}

?>