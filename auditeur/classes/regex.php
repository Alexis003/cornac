<?php

class regex extends modules {
	protected	$description = 'Liste des regex utilisées';
	protected	$description_en = 'List of regex';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}'
   FROM <tokens> T1
   JOIN <tokens> T2
   ON T2.fichier = T1.fichier AND
      T2.droite = T1.droite + 3
   WHERE T1.code in ('preg_match','preg_replace','preg_replace_callback','preg_match_all')
SQL;
        $this->exec_query($requete);

	}
}

?>