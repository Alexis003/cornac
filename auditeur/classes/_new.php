<?php

class _new extends modules {
	protected	$title = 'New';
	protected	$description = 'Liste des utilisations de l\'opérateur';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note new with literals 
        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}'
   FROM <tokens>  T1
   JOIN <tokens> T2
       ON T1.droite + 1 = T2.droite AND 
          T1.fichier = T2.fichier AND
          T2.type IN ('token_traite','variable')
   WHERE T1.type = '_new';
SQL;
        $this->exec_query($query);

// @note new with variables 
        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, TC.code, T1.id, '{$this->name}'
   FROM <tokens>  T1
   JOIN <tokens> T2
       ON T1.droite + 1 = T2.droite AND 
          T1.fichier = T2.fichier
   JOIN <tokens_cache> TC
       ON TC.id = T2.id
   WHERE T1.type = '_new';
SQL;
        $this->exec_query($query);

	}
}

?>