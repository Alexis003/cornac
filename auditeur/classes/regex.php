<?php

class regex extends modules {
	protected	$description = 'Liste des regex utilisées';
	protected	$description_en = 'List of regex';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM <rapport> WHERE module='{$this->name}'
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT 0, T1.fichier, T2.code, T1.id, '{$this->name}'
   JOIN rd T2
   ON T2.fichier = T1.fichier AND
      T2.droite = T1.droite + 3
   WHERE T1.code in ('preg_match','preg_replace','preg_replace_callback','preg_match_all')
SQL;

        $this->exec_query($requete);

	}
}

?>