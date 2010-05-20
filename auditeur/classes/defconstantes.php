<?php

class defconstantes extends modules {
	protected	$description = 'Liste des défintions de constantes';
	protected	$description_en = 'List of constante definition';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->format = modules::FORMAT_HTMLLIST;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
    SELECT 0, tokens.fichier, T3.code, tokens.id, '{$this->name}'
    FROM <tokens> T1
    JOIN tokens_tags
      ON T1.id = TT.token_id AND TT.type = 'fonction'
    JOIN <tokens> T2
      ON TT.token_sub_id = T2.id AND T2.fichier = T1.fichier
    JOIN <tokens> T3
      ON T3.droite = T2.droite + 3 AND T1.fichier = T3.fichier
    WHERE T1.type = 'functioncall' AND T2.code='define'
SQL;
        $this->exec_query($requete);

	}
}

?>