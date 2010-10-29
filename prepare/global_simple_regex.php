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

class global_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_GLOBAL);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkNotClass(array('variable','tableau'))) { return false; }

        $var = $t->getNext(1);

        while($var->checkCode(',')) {
            if ($var->getNext()->checkClass('variable')) {
                // @note registering a new global each comma
                    $args = array(0);
                    $remove = array(1);
                    
                    $repl = $var->getNext();
                    $var = $var->getNext(1);
                    
                    $regex = new modele_regex('_global',$args, $remove);
                    Token::applyRegex($repl, '_global', $regex);
                    continue;
                }
            // @note if we reach here, then something is wrong
            return false;
        }
        
        if ( $var->checkOperator(';') || 
             $var->checkToken(T_CLOSE_TAG) || 
             $var->checkClass('rawtext')
             ) {
            $this->args   = array(1);
            $this->remove = array(1, 2);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 

        // @note otherwise, fail
        return false;
    }
}

?>