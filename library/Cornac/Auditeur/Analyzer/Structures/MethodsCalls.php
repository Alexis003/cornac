<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011                                            |
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

class Cornac_Auditeur_Analyzer_Structures_MethodsCalls extends Cornac_Auditeur_Analyzer
 {
    protected $title = "Called methods";
    protected $description = "List method calls";

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_report();

        $query = <<<SQL
SELECT NULL, T1.file, T2.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_cache> T2 
    ON T1.id = T2.id
WHERE
    T1.type='method'
SQL;

        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>