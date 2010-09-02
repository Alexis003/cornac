<?php

class zfController extends modules {
	protected	$title = 'Controleurs ZF';
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T2.class", "'->'","T2.code");
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, $concat as code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
ON T1.id = TT.token_id AND
   TT.type='name'
JOIN <tokens> T2
ON T2.id = TT.token_sub_id AND
   T2.fichier=T1.fichier
WHERE T1.type = '_function' AND
T2.code like "%action"
ORDER BY T2.class, T2.code;
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>