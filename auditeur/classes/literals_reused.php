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

class literals_reused extends modules { 
	protected	$title = 'Literaux utilisés plusieurs fois';
	protected	$description = 'Literaux qui sont réutiisés à plusieurs endroits du code';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('literals');
	}

	public function analyse() {
        $this->clean_rapport();

// @note temporary table, so has to avoid concurrency conflict
        $query = <<<SQL
CREATE TEMPORARY TABLE {$this->name}_TMP 
SELECT TRIM(code) AS code
    FROM <tokens> TR1
    WHERE type = 'literals' AND 
          TRIM(code) != ''
    GROUP BY BINARY TRIM(code) 
    HAVING COUNT(*) > 1
SQL;
        $this->exec_query($query);

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TRIM(TR1.code), TR1.id, '{$this->name}', 0
    FROM <tokens> TR1
    JOIN {$this->name}_TMP TMP
        ON TR1.type = 'literals' AND 
           TMP.code = TRIM(TR1.code) 
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>