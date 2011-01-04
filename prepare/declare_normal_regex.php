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

class declare_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DECLARE);
    }
 
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        
        if ($t->getNext()->checkClass('parenthesis') && 
            $t->getNext(1)->checkOperator(';')) {
            $this->args = array(1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }

        if ($t->getNext()->checkClass('parenthesis') && 
            $t->getNext(1)->checkClass('block')) {
            $this->args = array(1, 2);
            $this->remove = array(1, 2);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        
        if ($t->getNext()->checkOperator('(') && 
            $t->getNext(1)->checkClass('arginit') &&
            $t->getNext(2)->checkCode(',') &&
            $t->getNext(3)->checkClass('arginit') &&
            $t->getNext(4)->checkOperator(')') &&
            $t->getNext(5)->checkNotOperator(':')
            ) {
            
            $this->args = array(2,4);
            $this->remove = array(1,2,3,4,5);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        
        
        return false;
    }
}
?>