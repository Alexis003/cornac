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
        $requete = <<<SQL
DELETE FROM rapport WHERE module='{$this->name}'
SQL;
        $this->mid->query($requete);

	    $requete = <<<SQL
INSERT INTO rapport 
    SELECT 0, tokens.fichier, T3.code, tokens.id, '{$this->name}'
    FROM tokens
    JOIN tokens_tags
      ON tokens.id = tokens_tags.token_id AND tokens_tags.type = 'fonction'
    JOIN tokens T2
      ON tokens_tags.token_sub_id = T2.id AND T2.fichier = tokens.fichier
    JOIN tokens T3
      ON T3.droite = T2.droite + 3 AND tokens.fichier = T3.fichier
    WHERE tokens.type = 'functioncall' AND T2.code='define'
SQL;
        $this->mid->query($requete);

	}
}

?>