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

class literals_long extends modules { 
	protected	$title = 'Literaux longs';
	protected	$description = 'Literaux qui sont trop longs (> 1ko)';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('literals');
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, TR1.file, TRIM(code), TR1.id, '{$this->name}', 0
FROM <tokens> TR1
WHERE type = 'literals' AND
      LENGTH(code) > 1024
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>