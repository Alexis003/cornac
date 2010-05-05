<?php

class _new extends modules {
	protected	$description = 'Liste des classes et de leurs extensions';
	protected	$description_en = 'List of classes et its extensions';

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
   FROM <tokens>  T1
   JOIN <tokens> T2
       ON T1.droite + 1 = T2.droite AND 
          T1.fichier = T2.fichier
   WHERE T1.type = '_new';
SQL;

        $this->exec_query($requete);

	}
}

?>