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

// @todo move this to codePHP?
class block_opening_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        return false;
        if (!$t->hasNext(2))           { return false; }
        if ($t->getNext()->checkClass('Token') )   { return false; }
        if ($t->getNext(1)->checkNotOperator(';') )   { return false; }
        if (!$t->getNext(2)->checkForBlock() &&
            !$t->getNext(2)->checkForVariable())   { return false; }

        $regex = new modele_regex('block',array(0), array(0, 1));
        Token::applyRegex($t->getNext(), 'block', $regex);
        
        mon_log(get_class($t)." => Block (".__CLASS__.")");
        return false;
    }
}
?>