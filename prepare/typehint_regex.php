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

class typehint_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ARRAY, T_STRING, Token::ANY_TOKEN);
    }

    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        if (!$t->hasPrev() ) { return false; }

        if ($t->getPrev()->checkNotOperator(array('(',','))) { return false; }
        if ($t->getPrev()->checkClass(array('arglist'))) { return false; }
        if ($t->getPrev(1)->checkToken(array(T_CATCH))) { return false; }
        if ($t->checkNotClass('Token')  &&  $t->checkToken(T_ARRAY)) { return false; }
        if ($t->checkToken(T_AS)) { return false; }
        // @note this is an interpolation ,with " : this won't be the only one.
        if ($t->checkOperator(array('"'))) { return false; } 

        if ($t->checkClass(array('variable'))) { return false; } 

        if ($t->getNext()->checkOperator(array('&')) &&
            $t->getNext(1)->checkClass('variable')) {

            if ( $t->getNext(2)->checkOperator(array('->','[','(','::'))) { return false; }
            
            if ($t->checkClass(array('constante','functioncall'))) {
                return false;
            }
            
            $regex = new modele_regex('reference',array(1), array(1));
            Token::applyRegex($t->getNext(), 'reference', $regex);

            mon_log(get_class($t->getNext())." => reference (".__CLASS__.")");
            return false;
        }
        
        if ($t->getNext()->checkNotClass(array('variable','affectation','reference'))) { return false; }
        if ($t->getNext(1)->checkCode(array('='))) { return false; }
        if ($t->getNext(1)->checkNotOperator(array(',',')'))) { return false; }
        
        $this->args = array(0,1);
        $this->remove = array(1);
        mon_log(get_class($t)." => ".__CLASS__."");
        
        return true;
    }
}
?>