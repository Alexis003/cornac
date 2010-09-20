<?php

class indenting extends modules {
	protected	$title = 'Indentations';
	protected	$description = 'Liste des niveaux d\'indentation nécessaires : les classes, fonctions, boucles et switch imposent généralement une indentation. Voici la liste des niveaux qui devraient être nécessaires, et la structure qui les impose.';


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