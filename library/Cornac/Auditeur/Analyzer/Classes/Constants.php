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

class Cornac_Auditeur_Analyzer_Classes_Constants extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Class constants';
	protected	$description = 'List of class constants, defined in the code';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
        $this->clean_report();

        $query = <<<SQL
SELECT NULL, T1.file, TC.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1 
JOIN <tokens_cache> TC 
    ON T1.id = TC.id
WHERE T1.type = "constant_class"
SQL;
        $this->exec_query_insert('report', $query);
	    return true;
	}
	
}

?>