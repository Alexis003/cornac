<?php

class variables_one_letter extends modules {
	protected	$title = 'Variables une lettre';
	protected	$description = 'Liste des variables dont le nom est une seule lettre';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}'
FROM <rapport> TR1
WHERE TR1.module = 'variables' AND LENGTH(REPLACE(TR1.element, '$','')) = 1
GROUP BY BINARY TR1.id;
SQL;
        $this->exec_query($query);

        return ;
	}
}

?>