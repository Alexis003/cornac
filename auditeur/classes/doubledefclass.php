<?php

class doubledefclass extends modules {
	protected	$description = 'Liste des défintions doubles de classes';
	protected	$description_en = 'List of double class definitions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	function dependsOn() {
        return array('classes');	
	}

	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, fichier, TR.element,  TR.token_id, '{$this->name}'
FROM <rapport> TR
 WHERE module='classes'
 GROUP BY element 
 HAVING count(*) > 1;
SQL;
    
        $this->exec_query($requete);

	}
}

?>