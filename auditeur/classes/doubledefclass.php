<?php

class doubledefclass extends modules {
	protected	$description = 'Liste des défintions doubles de classes';
	protected	$title = 'Double définitions de classes : des classes définies plusieurs fois au cours du code';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
        return array('classes');	
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, fichier, TR.element,  TR.token_id, '{$this->name}', 0
    FROM <rapport> TR
    WHERE module='classes'                                  AND
         TR.element IN (SELECT element FROM <rapport> TR
                            WHERE module='classes'
                            GROUP BY element 
                            HAVING count(*) > 1);
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>