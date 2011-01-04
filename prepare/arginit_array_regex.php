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

class arginit_array_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('=');
    }

    function check($t) {
        if (!$t->hasNext(2) ) { return false; }
        if (!$t->hasPrev() ) { return false; }

        if ($t->hasPrev(2) && $t->getPrev(1)->checkNotCode(array('(',','))) { return false; }
        if ($t->getPrev()->checkNotClass(array('variable','reference'))) { return false; }

        if ($t->getNext()->checkNotClass('functioncall')) { return false; }  // @note in fact, we just need accept arrays
        if ($t->getNext(1)->checkNotCode(array(',',')'))) { return false; }

        $this->args = array(-1, 1);
        $this->remove = array(-1, 1);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>