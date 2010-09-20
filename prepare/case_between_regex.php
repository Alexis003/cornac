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

class case_between_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function getTokens() {
        return array(T_DEFAULT,T_CASE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkGenericCase()) {
            if ($t->checkToken(T_CASE)) {
                if ($t->getNext()->checkClass(array('Token'))) { return false; }
                $var = $t->getNext(2);
                $init = $t->getNext(2);

                $this->args = array(0, 1 );
                $this->remove = array(1,2);
            } elseif ($t->checkToken(T_DEFAULT) || $t->checkClass('_default')) {
                $var = $t->getNext(1);
                $init = $t->getNext(1);

                $this->args = array(0 );
                $this->remove = array(1);
            } elseif ($t->checkClass('_case')) {
                return false; 
            } else {
                mon_log("Tentative de ".__CLASS__." => block mais '".$t."' n'est ni T_CASE, ni T_DEFAULT : ");
                return false;
            }
            $args = array();
            $remove = array();
            $pos = 0;
            
            while(!$var->checkGenericCase() && $var->checkNotCode('}') && $var->checkNotToken(T_ENDSWITCH)) {
                if ($var->checkCode(';')) { 
                    $remove[] = $pos;
                    $pos++;
                    $var = $var->getNext();
                    continue;
                }    
                if ($var->checkClass(array('Token'))) { return false; }
                if ($var->checkCode('{') && $var->checkNotClass('block'))      { return false; } // on attend que les structures soient traitées
                $args[] = $pos;
                $remove[] = $pos;
                $pos++;
                $var = $var->getNext();
            }
            
            if (empty($args)) { 
                // un case vide, mais un case quand même!
                // si c'était un token, on aurait déjà quitté
                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            } else {
                // nettoyage de la situation
                $this->args = array();
                $this->remove = array();
            }

            $regex = new modele_regex('block',$args, $remove);
            Token::applyRegex($init, 'block', $regex);

            mon_log(get_class($t)." => block (".__CLASS__.")");
            return false; 
        } 
        return false;
    }
}
?>