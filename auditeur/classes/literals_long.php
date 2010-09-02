<?php

class literals_long extends modules { 
	protected	$title = 'Literaux longs';
	protected	$description = 'Literaux qui sont trop longs (> 1ko)';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('literals');
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TRIM(code), TR1.id, '{$this->name}', 0
    FROM <tokens> TR1
    WHERE type = 'literals' AND
          LENGTH(code) > 1024
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>