<?php

class variables extends modules {
	protected	$title = 'Variables';
	protected	$description = 'Liste des variables utilisées dans l\'application';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type = 'variable' AND T1.code != '$'
SQL;
	$this->exec_query($query);
	
	}
}

?>