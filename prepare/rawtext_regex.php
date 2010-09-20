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

class rawtext_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_INLINE_HTML);
    }
    
    function check($t) {
        $this->args = array(0);
        $this->remove = array();
      
        if ($t->hasPrev() && $t->hasNext()) {
            if ($t->getPrev()->checkToken(T_CLOSE_TAG) &&
                $t->getNext()->checkToken(T_OPEN_TAG)) {
                
                if ($t->getNext(1)->checkCode('=')) {
                    // cas des short tags
                    return false;
                }
                $this->args = array(0);
                $this->remove = array(-1, 1);
            } elseif ($t->getPrev()->checkClass('codephp') &&
                      $t->getNext()->checkToken(T_OPEN_TAG)) {
                
                if ($t->getNext(1)->checkCode('=')) {
                    // cas des short tags
                    return false;
                } 
                $this->args = array(0);
                $this->remove = array(1);

            } elseif ($t->getPrev()->checkToken(T_CLOSE_TAG) &&
                      $t->getNext()->checkClass('codephp')) {
                $this->args = array(0);
                $this->remove = array(-1);
            } elseif ($t->getPrev()->checkClass('codephp') &&
                      $t->getNext()->checkClass('codephp')) {
                      // rien, on peut continuer
            } else {
            // rien du tout...
            }
        }

        mon_log(get_class($t)." => ".__CLASS__);
        return true;
    }
}
?>