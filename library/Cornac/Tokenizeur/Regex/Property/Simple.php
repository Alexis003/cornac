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

class Cornac_Tokenizeur_Regex_Property_Simple extends Cornac_Tokenizeur_Regex {
    protected $tname = 'property_regex';

    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OBJECT_OPERATOR);
    }
    
    function check($t) {
        if (!$t->hasPrev( ) ) { return false; }
        if (!$t->hasNext(1) ) { return false; }

        if (  $t->getPrev()->checkNotClass(array('variable',
                                                 'property',
                                                 '_array',
                                                 'method_static',
                                                 'method',
                                                 'functioncall',
                                                 'property_static',
                                                 'opappend')) ) { return false; }
        if (   $t->getPrev(1)->checkOperator(array('->','::'))) {
            return false; 
        }
// @note this avoid interfering with functioncall by detecting ( early enough not to make a literals
        if ( $t->getNext(1)->checkOperator('(')) { return false; }
        if ( $t->getNext(1)->checkClass('arglist')) { return false; }
        if ($t->getNext()->checkToken(T_STRING)) {
            $regex = new Cornac_Tokenizeur_Regex_Model('literals',array(0), array());
            Cornac_Tokenizeur_Token::applyRegex($t->getNext(), 'literals', $regex);
            return false;
        } elseif ( $t->getNext()->checkNotClass(array('variable','_array','literals'))) { 
            return false; 
        }

        $this->args   = array(-1, 1);
        $this->remove = array(-1,0, 1);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".$this->getTname());
        return true; 
    }
}
?>