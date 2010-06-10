<?php

class zfController extends modules {
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';
	protected	$description_en = 'List of action method from controlers in ZF ';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT 0, T1.fichier,concat(T2.class, '->',T2.code) as code, T1.id, '{$this->name}' 
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
    $this->exec_query($requete);
	}
}

?>