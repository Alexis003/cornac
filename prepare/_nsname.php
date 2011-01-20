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

define('T_NAMESPACED_NAME', 500);

class _nsname extends instruction {
    protected $namespace = array();
    
    function __construct($expression) {
        parent::__construct(array());
        
        foreach($expression as $e) {
            if ($e->checkToken(T_NS_SEPARATOR)) {
                $f = $this->makeProcessedToken('_nsseparator_',$e);
                $this->namespace[] = $f;
                $f->setCode('\\');
            } elseif ($e->checkClass('Token')) {
                $this->namespace[] = $this->makeProcessedToken('_nsname_', $e);
            } else {
                $this->namespace[] = $e;
            }
        }
    }

    function __toString() {
        return join('\\', $this->namespace);
    }

    function getNamespace() {
        return $this->namespace;
    }

    function neutralise() {
        foreach($this->namespace as $e) {
            $e->detach();
        }
    }

    function getRegex(){
        return array('nsname_normal_regex',
                    );
    }
    
    function getToken() {
        return T_NAMESPACED_NAME;
    }
}

?>