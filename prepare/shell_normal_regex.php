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

class shell_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
        
        $this->sequence_classes = array('literals',
                                        'variable',
                                        'tableau',
                                        'property',
                                        'property_static',
                                        'method',
                                        'method_static',
                                        'constante_static',
                                        'sequence',
                                        );
    }

    function getTokens() {
        return array('`');
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotOperator('`')) { return false; } 
        
        if ($t->checkOperator('`') ) {
            $token_fin = '`';
        } else {
            return false;
        }

        $var = $t->getNext(); 
        $this->args   = array(  );
        $this->remove = array(  );
        $pos = 1;
        
        while ($var->checkNotCode($token_fin)) {
            if ($var->checkCode('{') && 
                $var->getNext()->checkClass($this->sequence_classes) && 
                  $var->getNext(1)->checkCode('}')) {

                $regex = new modele_regex('variable',array(0), array(-1, 1));
                Token::applyRegex($var->getNext(), 'variable', $regex);

                mon_log(get_class($var->getNext())." => ".get_class($var->getNext())." (".__CLASS__.")");
                return false;
            }

            if ($var->checkNotClass($this->sequence_classes) &&
                $var->checkNotClass(array('sign'))) { return false; }
        
            $this->args[]    = $pos;
            $this->remove[]  = $pos;

            $pos += 1;
            $var = $var->getNext();
            if (is_null($var)) { return false; }
        }

        $this->remove[]  = $pos; // @note final "
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>