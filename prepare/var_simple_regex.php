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

class var_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkNotClass(array('variable','affectation'))) { return false; }

        $this->args = array(0, 1);
        $this->remove = array(1);

        if ($t->hasPrev() &&
            $t->getPrev()->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC))) {

            $this->args[] = -1;
            $this->remove[] = -1;

            sort($this->args);
            sort($this->remove);
        }
        
        $var = $t->getNext(1);
        
        if ($var->checkCode('=') &&
            $var->getNext()->checkClass(array('functioncall','literals','sign'))) {

                $args = array(0,1, 2);
                $remove = array(0,1, 2);
                    
                $regex = new modele_regex('affectation',$args, $remove);
                Token::applyRegex($t->getNext(), 'affectation', $regex);
                    
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." (".__CLASS__.")0  => Affectation");
                return false;
        }

        $i = 0;
        while($var->checkCode(',')) {
            if ($var->getNext()->checkClass('variable') && 
                $var->getNext(1)->checkCode('=') && 
                $var->getNext(2)->checkClass(array('literals','functioncall','_constant','sign'))) {

                    $args = array(0,1, 2);
                    $remove = array(0,1, 2);
                    
                    $regex = new modele_regex('affectation',$args, $remove);
                    Token::applyRegex($var->getNext(), 'affectation', $regex);
                    
                    Cornac_Log::getInstance('tokenizer')->log(get_class($t)." (".__CLASS__.")1  => Affectation");
                    return false;
            }

            if ($var->getNext()->checkClass(array('variable','affectation')) &&
                $var->getNext(1)->checkNotCode('=')) {
                    $i += 2;
                
                    $this->args[] = $i + 1;
                    $this->remove[] = $i;
                    $this->remove[] = $i + 1;
                    
                    if (!$var->hasNext(1)) { return false;}
                    $var = $var->getNext(1);

                    continue;
                }
                return false;
        }
        
        if ( $var->checkCode(';')) {
            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
            return true; 
        } elseif ($var->checkCode('=')) {
            if ($var->getNext()->checkClass(array('literals','functioncall','_constant','constant_static')) &&
                $var->getNext(1)->checkCode(';')) {

                $args   = array(0, 1, 2);
                $remove = array(0, 1, 2);

                $regex = new modele_regex('affectation',$args, $remove);
                Token::applyRegex($t->getNext(), 'affectation', $regex);
                    
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." (".__CLASS__.")2  => Affectation");
                return false;
            }
        } else {
            return false;
        }
        
        return false;
    }
}

?>