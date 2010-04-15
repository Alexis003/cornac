<?php

class ifsanselse extends modules {
	protected	$description = 'Liste des if sans else';
	protected	$description_en = 'if without else';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $requete = <<<SQL
DELETE FROM <rapport> WHERE module='{$this->name}'
SQL;
        $this->exec_query($requete);

	    $requete = <<<SQL
INSERT INTO <rapport>
   SELECT 0, T1.fichier, SUM(if (TT.type = 'else', 1, 0))  AS elsee, T1.id, '{$this->name}'
    FROM savelys_test T1
    LEFT join savelys_test_tags TT ON
        T1.id = TT.token_id
    WHERE T1.type = 'ifthen' 
    GROUP by fichier, droite
    HAVING elsee = 0;
SQL;
        $this->exec_query($requete);
	}
}

?>