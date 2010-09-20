<?php

class variables_lots_of_letter extends modules { 
	protected	$title = 'Long variable';
	protected	$description = 'Nom de variables avec trop de lettres (> 20)';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, TR1.fichier, CONCAT(TR1.element, ' (', LENGTH(TR1.element),' chars)' ), TR1.id, '{$this->name}', 0
FROM <rapport> TR1
WHERE TR1.module = 'variables' AND LENGTH(REPLACE(TR1.element, '$','')) > 19
GROUP BY BINARY TR1.id;
SQL;
        $this->exec_query_insert('rapport',$query);

        return true;
	}
}

?>