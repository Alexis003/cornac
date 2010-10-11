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

class comparison_constant extends modules {
	protected	$title = 'Static comparison';
	protected	$description = 'Comparison that have no variable part, nor is trying to guess the current PHP installation.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT('line ', T1.line, ' : ', T1.code), T1.id, '{$this->name}', 0
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite BETWEEN T1.droite AND T1.gauche AND
       ( T2.type = 'variable' OR
         T2.code = 'function_exists')
WHERE T1.type IN ( 'logique','comparison')
GROUP BY T1.id
HAVING COUNT(T2.id) = 0
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>