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

class Zf_Session extends modules {
	protected	$title = 'ZF : sessions';
	protected	$description = 'Using session in Zend Framework. Only Zend_Session_Namespace, no heritage supported. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('_new');
	}

	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT NULL, TR.file, TR.element, TR.id, '{$this->name}', 0
FROM <rapport> TR
WHERE module = '_new' AND
      element = 'Zend_Session_Namespace' 
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>