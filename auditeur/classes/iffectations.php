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

class iffectations extends modules {
	protected	$title = 'Affectations dans un if';
	protected	$description = 'Affectation dans un if';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL

SELECT NULL, T1.fichier, TC.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT
  ON T1.id = TT.token_id 
JOIN <tokens> T2
  ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id
JOIN <tokens> T3
  ON T1.fichier = T3.fichier AND T3.droite BETWEEN T2.droite AND T2.gauche
JOIN <tokens>_cache TC
  ON TC.id = T3.id
  WHERE T1.type='ifthen' AND
        TT.type = 'condition' AND
        T3.type = 'affectation' AND
        T1.fichier = './tests/auditeur/scripts/iffectations.php';
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>