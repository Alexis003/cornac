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

class sequence_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ( $t->checkClass('_case','_default'))                        { return false; }

        if ( $t->hasPrev() && $t->getPrev()->checkForAssignation())     { return false; }
        if ( $t->hasPrev() && $t->getPrev()->checkClass(array('parenthesis',
                                                        'arglist')))    { return false; }
        if ( $t->hasPrev() && $t->getPrev()->checkOperator(array('=',')','->','(',')',',','.','new','!==','::',':',
                '?','or','and','xor','var','$','/','+','-','*','%','@','&','|','^','"',
                '<','>','+','\\')))                                          { return false; }

        if ( $t->hasPrev() && $t->getPrev()->checkToken(array(T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC, T_VAR, T_THROW, 
                                                              T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR, 
                                                              T_BOOLEAN_OR, T_BOOLEAN_AND, 
                                                              T_IS_EQUAL, T_IS_SMALLER_OR_EQUAL, T_IS_NOT_IDENTICAL,
                                                              T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_GREATER_OR_EQUAL,
                                                              T_INSTANCEOF, T_ELSE, T_ABSTRACT, T_DO, T_CASE, T_DEFAULT,
                                                              T_CLONE, T_NAMESPACE, T_USE
                                                              ))                        )            { return false; }
        if ( $t->hasPrev()  && $t->getPrev( )->checkClass(array('_array','variable','property')))    { return false; }
        if ( $t->hasPrev(1) && $t->getPrev(1)->checkToken(array(T_FOR,T_WHILE)))                     { return false; }
        if ( $t->checkClass(array('_catch')))                                                        { return false; }

        if (($t->checkSubClass('instruction') || 
             $t->checkForVariable('instruction')) && 
            $t->checkNotClass('parenthesis') && 
            $t->getNext()->checkOperator(';') ) { 
                        
            $var = $t->getNext(1); 
            $this->args   = array( 0 );
            $this->remove = array( 1 );

            $pos = 2;

            if (is_null($var)) {
                mon_log(get_class($t)." => 0null ".__CLASS__);
                return true; 
            }
            if (!$var->hasNext()) {
                mon_log(get_class($t)." => 1null ".__CLASS__);
                
                return !$var->checkToken(T_CLOSE_TAG); 
            }

            while ($var->checkSubClass('instruction')) {
                $this->args[]    = $pos ;
                $this->remove[]  = $pos;
                
                $pos += 1;
                $var = $var->getNext();

                if (is_null($var)) {
                    mon_log(get_class($t)." => nnull ".__CLASS__);
                    return true; 
                }

                if ($var->checkOperator(';')) {
                    $this->remove[]  = $pos + 1;

                    $pos += 1;
                    $var = $var->getNext();
                    if (is_null($var)) {
                        mon_log(get_class($t)." => nnull2 ".__CLASS__);
                        return true; 
                    }
                } elseif ($var->checkToken(T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR)) {
                     return false;
                }
            }

            if ($var->checkOperator(array(',','->','::','[','(',',')) ||
                $var->checkForLogical() ||
                $var->checkForAssignation() ||
                $var->checkClass('arglist')) {
                // @doc This is not a sequence, as this operator finally has priority
                $this->args = array();
                $this->remove = array();
                return false;
            } elseif ($var->hasNext() && (
                $var->getNext()->checkOperator(array(',','->','::','[','(',',')) ||
                $var->getNext()->checkForAssignation() ||
                $var->getNext()->checkClass('arglist'))) {
                // @doc This is not a sequence, as another operator after has priority

                $this->args = array();
                $this->remove = array();
                return false;
            } elseif ($var->checkOperator(')')) {
                // @doc This looks like a for loop! 
                return false;
            } elseif (count($this->args) > 0) {
                // @doc OK we are good now
                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            } else {
                // @doc Not processed? aborting. 
                $this->args = array();
                $this->remove = array();
                return false;
            }
        }

        return false;
    }

}
?>