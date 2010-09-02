<?php

class interfaces extends noms {
	protected	$title = 'Interfaces';
	protected	$description = 'Liste des noms d\'interfaces définies';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T1.class, T1.id, '{$this->name}', 0
   FROM <tokens> T1
   WHERE T1.type = '_interface'
SQL;
        $this->exec_query($query);
        
        return true; 
	}
}

?>