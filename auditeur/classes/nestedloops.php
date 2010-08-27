<?php

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
INSERT INTO <rapport>
SELECT NULL, T1.fichier, $concat, T1.id, '{$this->name}' 
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T1.fichier = T2.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche
    WHERE T1.type in ('_while','_for','_foreach') AND T2.type IN  ('_while','_for','_foreach')
    GROUP BY T1.fichier, T1.droite, T2.type
    HAVING COUNT(*) > 1
SQL;

        $this->exec_query($query);
        
        return true;
	}
}

?>