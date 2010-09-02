<?php

class functions_without_returns extends noms {
	protected	$title = 'Fonctions sans return';
	protected	$description = 'Fonctions à qui il manque une commande de return. Elles ne retourne donc rien du tout.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note for methods
        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, CONCAT(T1.class,'::', T1.scope), T1.id, '{$this->name}', 0
   FROM <tokens> T1
   WHERE T1.class != '' AND
         T1.scope!='global'
   GROUP BY class, scope 
   HAVING SUM(if(type='_return', 1, 0)) = 0;
SQL;
        $this->exec_query($query);

// @note for functions
        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, CONCAT(T1.class,'::', T1.scope), T1.id, '{$this->name}', 0
   FROM <tokens> T1
   WHERE T1.class = '' 
   GROUP BY class, scope 
   HAVING SUM(if(type='_return', 1, 0)) = 0;
SQL;
        $this->exec_query($query);
        
        return true; 
	}
}

?>