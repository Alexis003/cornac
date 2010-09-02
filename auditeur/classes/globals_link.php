<?php

class globals_link extends modules {
	protected	$title = 'Réseau des globales';
	protected	$description = 'Liste des dépendances de globales entre les fichiers';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $query = <<<SQL
INSERT INTO <rapport_dot> 
SELECT DISTINCT TR1.fichier, TR2.fichier, TR1.element, '{$this->name}'
FROM ach_rapport TR1
JOIN ach_rapport TR2
    ON TR1.element = TR2.element AND
       TR2.module='globals'
WHERE TR1.module='globals'
SQL;
        $res = $this->exec_query($query);
	}
}

?>