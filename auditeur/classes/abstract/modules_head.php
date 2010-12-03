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

abstract class modules_head extends modules  {
    function __construct($mid) {
        parent::__construct($mid);
    }

	function dependsOn() {
        $dependencies = glob(dirname(dirname(__FILE__))."/".get_class($this)."/*");
        foreach($dependencies as $id => $d) {
            $d = str_replace(dirname(dirname(__FILE__))."/",'', $d);
            $d = str_replace('.php','', $d);
            $d = str_replace('/','_', $d);
            $dependencies[$id] = $d;
        }
        
        return $dependencies;
	}
    
	public function analyse() {
        return true;
	}

}
?>