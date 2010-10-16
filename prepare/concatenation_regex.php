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

class concatenation_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(".");
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }
        if (!$t->hasPrev( 1 )) { return false; }

        if ($t->getPrev()->checkClass(array('Token','arglist'))) { return false; }
        if ($t->getPrev(1)->checkOperator(array('.','->','@','::','++','--'))) { return false; }
        
        $var = $t; 
        $this->args   = array( -1 );
        $this->remove = array( -1 );
        
        $pos = 0;
        
        while ($var->checkCode('.') && 
               $var->getNext()->checkNotClass(array('Token','arglist'))) {

            $this->args[]    = $pos + 1;

            $this->remove[]  = $pos;
            $this->remove[]  = $pos + 1;
            
            $pos += 2;
            $var = $var->getNext(1);
        }
        
        if ($var->checkEndInstruction()) {
            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } else {
            $this->args = array();
            $this->remove = array();
            return false;
        }
    }
}
?>