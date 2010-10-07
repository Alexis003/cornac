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

class multidimarray extends modules {
	protected	$title = 'Multi-dimensionnal arrays';
	protected	$description = 'List of arrays that are multidimensionnal : $x[1][2], $x[1][2][3], and more.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note the comment /* JOIN */ here is important
	    $query = <<<SQL
SELECT NULL, T1.fichier, TC.code ,T1.id, '{$this->name}', 0
FROM <tokens> T1
/* JOIN */
JOIN <tokens_cache> TC
    ON TC.id = T1.id
LEFT JOIN <tokens> TX
    ON TX.type IN ('tableau','opappend') AND 
       T1.fichier = TX.fichier AND
       T1.droite - 1 = TX.droite
LEFT JOIN <rapport> TR
    ON TR.module='{$this->name}' AND
       TR.token_id = T1.id
WHERE T1.type IN ('tableau','opappend') AND
      TR.id IS NULL AND
      TX.id IS NULL
SQL;

for($i = 2; $i < 7; $i++) {
    $h = $i - 1;
    $join = <<<SQL
JOIN <tokens> T$i
    ON T$i.type IN ('tableau','opappend') AND 
       T1.fichier = T$i.fichier AND
       T$h.droite + 1 = T$i.droite
/* JOIN */
SQL;
    $query = str_replace('/* JOIN */', $join, $query);
    $query = str_replace('       T'.$h.'.droite + 1 = TX.droite','       T'.$i.'.droite + 1 = TX.droite', $query);

    $this->exec_query_insert('rapport', $query);
}

        // @todo spot array(array());
        
        return true;
	}
}

?>