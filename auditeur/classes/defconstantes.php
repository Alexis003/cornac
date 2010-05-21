<?php

class defconstantes extends modules {
	protected	$description = 'Liste des défintions de constantes';
	protected	$description_en = 'List of constante definition';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->format = modules::FORMAT_HTMLLIST;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT 0, T1.fichier, T3.code, T3.id, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2
    ON T1.droite + 1 = T2.droite
       AND T1.fichier=  T2.fichier
    JOIN <tokens> T3
    ON T1.droite + 4 = T3.droite
       AND T1.fichier=  T3.fichier
    WHERE T1.type='functioncall' AND
          T2.code = 'define';
SQL;
        $this->exec_query($requete);

	}
}

?>