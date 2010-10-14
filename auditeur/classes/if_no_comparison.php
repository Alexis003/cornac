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

class if_no_comparison extends modules {
	protected	$title = 'if without comparison';
	protected	$description = 'Spot if conditions without explicit comparison, like if ($x) or if (count($t))';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

// @doc check for everything except logique and (not or noscream)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}', 0
FROM <tokens> T1 
JOIN <tokens> T2 
ON T2.fichier = T1.fichier AND
   T2.droite = T1.droite + 2 AND
   T2.type NOT IN ('logique','not','noscream')
WHERE T1.type IN ('ifthen', '_while')
SQL;
        $this->exec_query_insert('rapport', $query);

// @doc check for everything in a not or noscream except logique
// @not one can mix not and noscream.... 
	    $query = <<<SQL
SELECT NULL, T1.fichier, T3.code, T1.id, '{$this->name}', 0
FROM <tokens> T1 
JOIN <tokens> T2 
ON T2.fichier = T1.fichier AND
   T2.droite = T1.droite + 2 AND
   T2.type IN ('not','noscream')
JOIN <tokens> T3
ON T3.fichier = T1.fichier AND
   T3.droite = T1.droite + 3 AND
   T3.type NOT IN ('logique')
WHERE T1.type IN ('ifthen', '_while')
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>