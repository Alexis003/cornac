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

class function_typehintreference_regex extends analyseur_regex {
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
            if (($var->checkClass('constante') ||
                 $var->checkToken(T_ARRAY)) &&
                $var->getNext()->checkOperator('&') &&
                $var->getNext(1)->checkClass('variable')) {
                
                if ($var->getNext(2)->checkCode('=') &&
                    $var->getNext(3)->checkNotClass('Token')) {
                          
                        $regex = new modele_regex('reference',array(1), array(1));
                        Token::applyRegex($var->getNext(), 'reference', $regex);
    
                        mon_log(get_class($t)." => reference 1 (".__CLASS__.")");

                        $regex = new modele_regex('affectation',array(0, 1, 2), array(1, 2));
                        Token::applyRegex($var->getNext(), 'affectation', $regex);
    
                        mon_log(get_class($t)." => affectation (".__CLASS__.")");

                        $regex = new modele_regex('typehint',array(0, 1), array(1));
                        Token::applyRegex($var, 'typehint', $regex);
    
                        mon_log(get_class($t)." => typehint = (".__CLASS__.")");

                        $var = $var->getNext();
                        if (is_null($var)) { return false; }
                        continue; 
                } elseif ($var->getNext(2)->checkNotCode('=')) {
                    $regex = new modele_regex('reference',array(1), array(1));
                    Token::applyRegex($var->getNext(), 'reference', $regex);
    
                    mon_log(get_class($t)." => reference 2 (".__CLASS__.")");                    
                    
                    $regex = new modele_regex('typehint',array(0, 1), array(1));
                    Token::applyRegex($var, 'typehint', $regex);
    
                    mon_log(get_class($t)." => typehint init =2 (".__CLASS__.")");
                    
                    $var = $var->getNext();
                    if (is_null($var)) { return false; }
                    continue; 
                } elseif ($var->getNext(2)->checkCode(array(',',')'))) {
                    $regex = new modele_regex('reference',array(1), array(1));
                    Token::applyRegex($var->getNext(), 'reference', $regex);
    
                    mon_log(get_class($t)." => reference 3 (".__CLASS__.")");

                    $regex = new modele_regex('typehint',array(0, 1), array(1));
                    Token::applyRegex($var, 'typehint', $regex);
    
                    mon_log(get_class($t)." => typehint =3 (".__CLASS__.")");
                    
                    $var = $var->getNext();
                    if (is_null($var)) { return false; }
                    continue; 
                } 
            }
            // cas des typehint avec initialisation
            
            if ($var->checkOperator('(')) {
                // @note typehing can't be followed by an opening bracket
                return false; 
            }
            
            // @note there must be something beyond...
            if (!$var->hasNext()) { return false; }
            $var = $var->getNext();
            if (is_null($var)) { return false; }
        }

        return false;
    }
}

?>