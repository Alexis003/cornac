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

class arglist_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('(');
    }
 
    function check($t) {
        if (!$t->hasPrev(  )) { return false; }

        // @note for it to be a function call, one need all this before
        if ($t->getPrev()->checkNotFunction() &&
            $t->getPrev()->checkNotClass(array('variable','tableau')) &&
            $t->getPrev()->checkNotCode('}')) { return false;}
        
       if ($t->getPrev()->checkCode('}') && 
        // @todo could be limit getPrev(1) values? 
        $t->getPrev(2)->checkNotCode('{')) {
                return false;
            }
            
        $var = $t->getNext(); 
        $this->args   = array();
        $this->remove = array();
        
        $pos = 1;
        
        while ($var->checkNotClass('Token') && 
               $var->checkNotOperator(')')  &&
               $var->getNext()->checkOperator(',')) {
            $this->args[]    = $pos;
            $this->remove[]  = $pos;
            $this->remove[]  = $pos + 1;
            
            $pos += 2;
            $var = $var->getNext();
            if ($var->checkOperator('(')) { return false; }
            $var = $var->getNext();
            if ($var->checkOperator('(')) { return false; }
        }
        

        if ($var->checkOperator(')')) {
            $this->remove[] = $pos; // @note remove the final )
            
            mon_log(get_class($t)." =>1 ".__CLASS__);
            return true; 
        } elseif ($var->getNext()->checkOperator(')')) {
            if ($var->checkClass('Token')) { return false; }
            
            if ($t->getPrev()->checkCode('echo') && 
                $var->getNext(1)->checkCode(array('|','&','^'))) { return false; }
            
            $this->args[]    = $pos ;

            $this->remove[]  = $pos ;
            $this->remove[]  = $pos + 1;

            mon_log(get_class($t)." =>2 ".__CLASS__);
            return true; 
        } else {
            $this->args = array();
            $this->remove = array();
            return false;
        }
    }
}
?>