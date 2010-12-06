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

class nsname_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_NS_SEPARATOR);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if (!$t->hasPrev()) { return false; }

// @note NSname may actually start by \ \htmlentities
        if ($t->getPrev()->checkToken(array(T_STRING))) { 
            $this->args = array(-1);
            $this->remove = array(-1);
        } 

        if ($t->getNext()->checkNotClass('Token')) { return false; }
        $this->args[] = 1;
        $this->remove[] = 0;
        $this->remove[] = 1;
        
        $var = $t->getNext(1);
        $pos = 1;
        while($var->checkOperator('\\')) {
            $this->args[] = $pos + 2;
            $this->remove[] = $pos + 1;
            $this->remove[] = $pos + 2;
            
            $var = $var->getNext(1);
            $pos += 2;
        }
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>