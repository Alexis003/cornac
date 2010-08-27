<?php

class doubledeffunctions extends modules {
	protected	$title = 'Défintions de fonctions en double';
	protected	$description = 'Liste des défintions doubles de fonctions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	function dependsOn() {
        return array('deffunctions');	
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, fichier, TR.element,  TR.token_id, '{$this->name}'
FROM <rapport> TR
 WHERE module='deffunctions'
 GROUP BY element 
 HAVING count(*) > 1;
SQL;
    
        $this->exec_query($query);
        return true;
	}
}

?>