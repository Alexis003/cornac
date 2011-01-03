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

class sequence_suite_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotClass('sequence')) { return false; }
        if ($t->getNext()->checkForBlock(true) || 
            $t->getNext()->checkClass(array('parenthesis','codephp'))) { 

            $var = $t->getNext(1); 
            $this->args   = array( 0, 1 );
            $this->remove = array( 1 );
            
            $pos = 2;
            
            if (is_null($var)) {
                mon_log(get_class($t)." fusionne ".count($this->args)." sequences (avant, 1,  ".__CLASS__.")");
                return true; 
            }
            
            while ($var->checkForBlock(true) || $var->checkClass(array('codephp')) ) {
                $this->args[]    = $pos ;
                
                $this->remove[]  = $pos;
                $pos += 1;
                $var = $var->getNext();
                if (is_null($var)) {
                    mon_log(get_class($t)." fusionne ".count($this->args)." sequences (avant, 2, ".__CLASS__.")");
                    return true; 
                }
            } 
            
            if ($var->checkForAssignation() ||
                $var->checkCode(array('or','and','xor','->','[','::',')','.','||','&&'))) {
                $this->args = array();
                $this->remove = array();
                return false;
            }
            
            mon_log(get_class($t)." fusionne ".count($this->args)." sequences (avant, 3, ".__CLASS__.")");
            return true; 
        } 
        
        $this->args = array();
        $this->remove = array();
        return false;
    }

}
?>