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

class function_typehint_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FUNCTION);
    }
    
    function check($t) {
        if (!$t->hasNext(3)) { return false; }

        $var = $t->getNext(2);
        
        while ($var->checkNotOperator(')')) {
            if (!$var->hasNext()) { return false; }
            if (($var->checkClass('constante') ||
                 $var->checkToken(array(T_ARRAY,T_STRING))) &&
                 $var->getNext()->checkClass('variable')) {
                
                if ($var->getNext(1)->checkOperator('=') &&
                    $var->getNext(2)->checkNotClass('Token')) {
                        $regex = new modele_regex('affectation',array(0, 1, 2), array(1, 2));
                        Token::applyRegex($var->getNext(), 'affectation', $regex);

                        mon_log(get_class($t)." => affectation (".__CLASS__.")");

                        $regex = new modele_regex('typehint',array(0, 1), array(1));
                        Token::applyRegex($var, 'typehint', $regex);

                        mon_log(get_class($t)." => typehint (".__CLASS__.")");

                        $var = $var->getNext();
                        continue; 
                } elseif ($var->getNext(1)->checkOperator('=')) {
                    if ($var->getNext(2)->checkClass('Token')) { return false; }
                    if ($var->getNext(3)->checkClass('arglist')) {
                        $regex = new modele_regex('functioncall',array(0, 1), array(1));
                        Token::applyRegex($var->getNext(2), 'functioncall', $regex);

                        mon_log(get_class($t)." => affectation (".__CLASS__.")");
                        // @note return? 
                    }
                    
                    $regex = new modele_regex('affectation',array(0, 1, 2), array(1, 2));
                    Token::applyRegex($var->getNext(), 'affectation', $regex);

                    mon_log(get_class($t)." => affectation (".__CLASS__.")");

                    $regex = new modele_regex('typehint',array(0, 1), array(1));
                    Token::applyRegex($var, 'typehint', $regex);
    
                    mon_log(get_class($t)." => typehint = (".__CLASS__.")");
                    
                    $var = $var->getNext();
                    continue; 
                } elseif ($var->getNext(1)->checkOperator(array(',',')'))) {
                    $regex = new modele_regex('typehint',array(0, 1), array(1));
                    Token::applyRegex($var, 'typehint', $regex);
    
                    mon_log(get_class($t)." => typehint ,) (".__CLASS__.")");
                    
                    $var = $var->getNext();
                    continue; 
                } 
            }

            // @note typehint with initialisation
            if ($var->checkOperator('(')) {
                // @note avoiding collision with other structures
                return false; 
            }
            
            if (!$var->hasNext()) { return false; }
            $var = $var->getNext();
        }

        return false;
    }
}

?>