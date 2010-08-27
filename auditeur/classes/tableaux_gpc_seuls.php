<?php

class tableaux_gpc_seuls extends modules {
	protected	$title = 'Tableaux GPC';
	protected	$description = 'Liste des tableaux spéciaux de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note cas simple : variable -> method
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    LEFT JOIN <tokens> T2 ON T1.droite - 1 = T2.droite AND T1.fichier = T2.fichier
    WHERE T1.type="variable" AND
          T1.code IN ('\$_GET','\$_SERVER','\$GLOBALS','\$_POST','\$_REQUEST','\$_ENV','\$_COOKIE','\$_SESSION')
           AND T2.type != 'tableau'
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>