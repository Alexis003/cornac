<?php

class indenting extends modules {
	protected	$description = 'Liste des niveaux d\'indentation';
	protected	$description_en = 'List of indentation levels';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
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
INSERT INTO <rapport> 
SELECT NULL, N.fichier, GROUP_CONCAT(P.type ORDER BY P.droite) AS code, N.id, '{$this->name}'
FROM <tokens> N, <tokens> P 
WHERE N.type IN ('ifthen','_class','_function','_while','_dowhile','_foreach','_case','_for','_switch') AND
      N.fichier = P.fichier AND
      N.droite BETWEEN P.droite AND P.gauche AND
      P.type IN ('ifthen','_class','_function','_while','_dowhile','_foreach','_case','_for','_switch')
      GROUP BY N.id
SQL;
        $this->exec_query($query);

        return ;
	}
}

?>