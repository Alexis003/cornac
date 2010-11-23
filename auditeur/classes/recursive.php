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

class recursive extends modules {
	protected	$title = 'Titre pour recursive';
	protected	$description = 'Ceci est l\'analyseur recursive par défaut. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT NULL, T1.file, CONCAT('::',T1.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T1.right + 1 = T2.right AND
       T2.type = 'method'
WHERE T1.type = 'functioncall' AND 
      T1.class = '' AND 
      T1.scope=T1.code  AND
      T2.id IS NULL;
SQL;
        $this->exec_query_insert('rapport', $query);

	    $query = <<<SQL
SELECT NULL, T1.file, CONCAT(T1.class,'::',T1.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND
       T1.right + 1 = T2.right AND
       T2.type = 'method'
WHERE T1.type = 'functioncall' 
      AND T1.class != ''
      AND T1.scope=T1.code;
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>