<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011                                            |
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

class Cornac_Tokenizeur_Regex_Ifthen_Block extends Cornac_Tokenizeur_Regex {
    protected $tname = 'ifthen_block_regex';

    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }

        if ($t->getNext()->checkNotClass('parenthesis')) { return false; }
        if ($t->getNext(1)->checkNotClass('block')) { return false; } 
        
        if ($t->hasNext(2) && $t->getNext(2)->checkToken(array(T_ELSE, T_ELSEIF))) { return false; }
        if ($t->hasNext(2) && $t->getNext(2)->checkOperator(':')) { return false; }

        $this->args   = array(1, 2);
        $this->remove = array(1, 2);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".$this->getTname());
        return true; 
    }
}
?>