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

class constant_class_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CONST);
    }
    
    function check($t) {
        if (!$t->hasNext(3) ) { return false; }

        if ( $t->checkNotToken(T_CONST)) { return false; }
        if ( $t->getNext()->checkNotClass('_constant')) { return false; }
        if ( $t->getNext(1)->checkNotOperator('=')) { return false; }
        if ( $t->getNext(2)->checkClass('Token')) { return false; }

        $var = $t->getNext(3);
        
        while($var->checkOperator(',')) {
            if ($var->getNext()->checkClass('_constant') &&
                $var->getNext(1)->checkCode('=') &&
                $var->getNext(2)->checkNotClass('Token')) {
                
                    $args = array(0,2);
                    $remove = array(1,2,3);
                    
                    $repl = $var->getNext();
                    $var = $var->getNext(3);
                    
                    $regex = new modele_regex('constant_class',$args, $remove);
                    Token::applyRegex($repl, 'constant_class', $regex);
                    continue;
                }
            // @note if we reach here, there is a problem
            return false;
        }
        
        if ( $var->checkOperator(';')) {
            $this->args   = array(1, 3);
            $this->remove = array(1, 2, 3, 4);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
            return true; 
        }
    }
}
?>