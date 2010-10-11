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

class indenting extends modules {
	protected	$title = 'Indentations';
	protected	$description = 'List of indentation level expected. We expect a new level with the following scopes : classes, functions, loops and switch.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_rapport();

/* @example
+---------+----------+--------------------------------------------------------------+
| id      | COUNT(*) | GROUP_CONCAT(P.type ORDER BY P.droite)                       |
+---------+----------+--------------------------------------------------------------+
| 1754692 |        1 | ifthen                                                       |
| 1754718 |        1 | ifthen                                                       |
| 1754765 |        1 | ifthen                                                       |
| 1754802 |        2 | ifthen,ifthen                                                |
| 1754897 |        2 | ifthen,ifthen                                                |

*/
        $query = <<<SQL
SELECT NULL, N.fichier, GROUP_CONCAT(P.type ORDER BY P.droite) AS code, N.id, '{$this->name}', 0
FROM <tokens> N, <tokens> P 
WHERE N.type IN ('ifthen','_class','_function','_while','_dowhile','_foreach','_case','_for','_switch') AND
      N.fichier = P.fichier AND
      N.droite BETWEEN P.droite AND P.gauche AND
      P.type IN ('ifthen','_class','_function','_while','_dowhile','_foreach','_case','_for','_switch')
      GROUP BY N.id
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>