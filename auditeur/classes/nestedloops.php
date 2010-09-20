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

class nestedloops extends modules {
	protected	$title = 'Boucles imbriquées';
	protected	$description = 'Liste des boucles';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T1.type","'->'","T2.type");
        $query = <<<SQL
SELECT NULL, T1.fichier, $concat, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche
WHERE T1.type in ('_while','_for','_foreach') AND T2.type IN  ('_while','_for','_foreach')
GROUP BY T1.fichier, T1.droite, T2.type
HAVING COUNT(*) > 1
SQL;

        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>