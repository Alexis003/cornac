<?php

class interfaces extends noms {
	protected	$title = 'Interfaces';
	protected	$description = 'Liste des noms d\'interfaces définies';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T1.class, T1.id, '{$this->name}'
   FROM <tokens> T1
   WHERE T1.type = '_interface'
SQL;
        $this->exec_query($requete);
        
        return; 
	}
}

?>