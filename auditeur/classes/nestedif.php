<?php

class nestedif extends modules {
	protected	$description = 'Liste des if';
	protected	$description_en = 'List of if';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T1.type","'->'","T2.type");
        $requete = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, $concat, T1.id, '{$this->name}' 
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T1.fichier = T2.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche
    WHERE T1.type in ('ifthen') AND T2.type IN  ('ifthen')
    GROUP BY T1.fichier, T1.droite 
    HAVING COUNT(*) > 1
SQL;

        $this->exec_query($requete);

	}
}

?>