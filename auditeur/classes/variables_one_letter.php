<?php

class variables_one_letter extends modules {
	protected	$description = 'Liste des variables avec une seule variable';
	protected	$description_en = 'List of variables names with only one letter';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}'
FROM <rapport> TR1
WHERE TR1.module = 'variables' AND LENGTH(REPLACE(TR1.element, '$','')) = 1
GROUP BY TR1.id;
SQL;
        $this->exec_query($requete);

        return ;
	}
}

?>