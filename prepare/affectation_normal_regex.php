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

class affectation_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if (!$t->checkForAssignation()) { return false; }
        
        if ( $t->getNext(1)->checkNotCode(array(';','}',')',',',':',']')) &&
             $t->getNext(1)->checkNotClass(array('sequence','block','_foreach','_for','rawtext')) &&
             $t->getNext(1)->checkNotToken(array(T_AS,T_CLOSE_TAG))
                ) { return false;}
                
        if ($t->hasPrev(1) && $t->getPrev(1)->checkCode(array('&','$','::','@','->','var','public','private','protected'))) { return false;}
        if (($t->getPrev()->checkClass(array('variable','property','opappend','functioncall','not','noscream','property_static','reference','cast')) || 
             $t->getPrev()->checkSubclass('variable')) &&
            ($t->getNext()->checkClass(array('literals', 'variable','_array','sign','noscream',
                                             'property', 'method'  ,'ternaryop',
                                             'functioncall','operation','logique',
                                             'method_static','operation','ternaryop',
                                             'constante_static','property_static','_clone',
                                             'parentheses','_new','cast','constante','invert',
                                             'not','affectation','shell','bitshift','comparison',
                                             'reference','concatenation','variable',
                                             'property_static','postplusplus','preplusplus','inclusion',
                                             '_closure')))
            
            ) {
                $this->args = array(-1, 0, 1);
                $this->remove = array( -1, 1);
                
                mon_log(get_class($t)." => ".__CLASS__);
                return true;
            } else {
                return false;
            }
    }
}

?>