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

class Variables_Gpc extends typecalls {
	protected	$title = 'Web variables';
	protected	$description = 'Usage of web variables : those variables, set by PHP, and coming from external sources.';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
	    $this->type = 'variable';
	    $this->code = array('$_GET','$_POST','$_COOKIE','$_SERVER','_FILES','$_REQUEST','$_SESSION','$_ENV',
	                        '$PHP_SELF','$HTTP_RAW_POST_DATA',
	                        '$HTTP_GET_VARS','$HTTP_POST_VARS',
	                        '$GLOBALS');
	    parent::analyse();
	}
	
}

?>