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


class Cornac_Auditeur_Analyzer_Commands_Path extends Cornac_Auditeur_Analyzer_Names {
	protected	$title = 'Paths';
	protected	$description = 'Spot strings that look like a path : it has / or \\ in it.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

// @todo add checks based on variables used in fopen,realpath, etc.
// @todo may be check for frequent folder (inc, public, html, inc...) ? 

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type = 'literals' AND
      (T1.code LIKE "%\\%" OR  T1.code LIKE "%/%")
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>