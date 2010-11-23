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

class tableaux_gpc extends modules {
	protected	$title = 'Super global array, and their index';
	protected	$description = 'PHP super global usage, and their corresponding index.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note : simple situation : variable -> method
        $query = <<<SQL
SELECT NULL, T1.file, TC.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1 
JOIN <tokens> T2 
    ON T1.left + 1 = T2.left AND T1.file = T2.file
JOIN <tokens_cache> TC 
    ON T1.id = TC.id
WHERE T1.type="_array" AND
      T2.code IN ('\$_GET','\$_SERVER','\$GLOBALS','\$_POST','\$_REQUEST','\$_ENV','\$_COOKIE','\$_SESSION')
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>