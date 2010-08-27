<?php

class gpc_variables extends modules {
	protected	$title = 'Variables Web';
	protected	$description = 'Liste les variables provenant du web, manipulées dans $_GET, $_FILES, $_POST, $_COOKIE';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T3.code, T1.id, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T2.droite = T1.droite + 1 AND
           T1.fichier = T2.fichier AND
           T2.type = 'variable' AND
           T2.code IN ('\$_GET','\$_REQUEST','\$_POST','\$_COOKIE','\$_FILES')
    JOIN <tokens> T3
        ON T3.droite = T2.gauche + 1 AND
           T3.gauche < T1.gauche     AND
           T1.fichier = T3.fichier
    WHERE T1.type='tableau'
SQL;
        $res = $this->exec_query($query);
	}
}

?>