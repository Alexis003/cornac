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

class ifthenelse_simples_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ELSE);
    }
    
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotToken(T_ELSE)) { return false;}

        if ($t->getNext()->checkForBlock(true) && 
            (!$t->hasNext(1) || 
              ($t->getNext(1)->checkCode(';') ||
               $t->getNext(1)->checkToken(T_CLOSE_TAG)))
            ) {

            $regex = new modele_regex('block',array(0), array(1));
            Token::applyRegex($t->getNext(), 'block', $regex);

            mon_log(get_class($t)." => block (from ".get_class($t).") (".__CLASS__.")");
            return false; 
        } 

        if ( ($t->getNext()->checkForBlock(true) ||
              $t->getNext()->checkClass(array('concatenation','constante','sign','not','noscream','invert','parentheses')) ||
              $t->getNext()->checkForVariable()
              )
            ) {

            if ($t->getNext(1)->checkForAssignation()) { return false; }
            if ($t->getNext(1)->checkCode(array('.','->','[','::'))) { return false; }
            if ($t->getNext(1)->checkClass('Token') &&
                $t->getNext(1)->checkNotEndInstruction()) { return false; }
            
            $regex = new modele_regex('block',array(0), array());
            Token::applyRegex($t->getNext(), 'block', $regex);

            mon_log(get_class($t)." => block (from instruction) (".__CLASS__.")");
            return false; 
        } 

        return false;
    }
}
?>