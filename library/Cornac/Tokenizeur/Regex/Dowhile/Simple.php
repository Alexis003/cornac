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

class Cornac_Tokenizeur_Regex_Dowhile_Simple extends Cornac_Tokenizeur_Regex {
    protected $tname = 'dowhile_simple_regex';

    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DO);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if (  $t->getNext()->checkClass('block'))   { return false; }
        if ( !$t->getNext()->checkForBlock()) { return false; }
        if (  $t->getNext(1)->checkNotOperator(';')) { return false; }

        $args = array(0);
        $remove = array(1);
        
        $regex = new Cornac_Tokenizeur_Regex_Model('block',$args, $remove);
        Cornac_Tokenizeur_Token::applyRegex($t->getNext(), 'block', $regex);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (".$this->getTname().")");
        return false; 
    }
}
?>