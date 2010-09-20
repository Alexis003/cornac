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

class method_accolade_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(3) ) { return false; }

        if ( ($t->checkClass(array('variable','property','tableau','method','method_static','functioncall')) ) && 
              $t->getNext()->checkCode('->') &&
              $t->getNext(1)->checkCode('{') &&
              $t->getNext(2)->checkNotClass('Token') &&
              $t->getNext(3)->checkCode('}')) {
              
              if ( $t->getNext(4)->checkCode('(') &&
                   $t->getNext(5)->checkCode(')')) {
        
                   $regex = new modele_regex('functioncall',array(0), array(-1, 1, 2));
                   Token::applyRegex($t->getNext(2), 'functioncall', $regex);

                    mon_log(get_class($t)." => functioncall (".__CLASS__.")");
                    return false; 
              }

              if ( $t->getNext(4)->checkClass('arglist')) {
                   $regex = new modele_regex('functioncall',array(0, 2), array(-1, 1, 2));
                   Token::applyRegex($t->getNext(2), 'functioncall', $regex);

                    mon_log(get_class($t)." => functioncall (".__CLASS__.")");
                    return false; 
              }

              return false;
        } 
        return false;
    }
}
?>