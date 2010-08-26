<?php

class variables_unaffected extends modules {
	protected	$title = 'Variables jamais initialisées';
	protected	$description = 'Liste des variables utilisées sans initialisation';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }
    
    function dependsOn() {
        return array('variables', 'affectations_variables');
    }

	public function analyse() {
	    // @question : isn't TR1.fichier = TR2.fichier too restrictive? 
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, TR1.fichier, TR1.element AS code, TR1.token_id, '{$this->name}'
FROM <rapport> TR1
LEFT JOIN <rapport> TR2
    ON TR2.module = 'affectations_variables' AND TR1.element = TR2.element AND TR1.fichier = TR2.fichier
WHERE TR1.module='variables' AND TR1.element NOT IN ('\$GLOBALS','\$_SESSION','\$_GET','\$_POST','\$this') AND TR2.element IS NULL
SQL;
    	$this->exec_query($query);
	    return true;
	}
}

?>