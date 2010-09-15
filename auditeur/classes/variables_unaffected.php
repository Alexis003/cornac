<?php

class variables_unaffected extends modules {
	protected	$title = 'Variables jamais initialisées';
	protected	$description = 'Liste des variables utilisées sans initialisation';

    function __construct($mid) {
        parent::__construct($mid);
    }
    
    function dependsOn() {
        return array('variables', 'affectations_variables','keyval');
    }

	public function analyse() {
	    // @question : isn't TR1.fichier = TR2.fichier too restrictive? ç
	    // @todo take scope/class into account
	    // @todo take foreach into account
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, TR1.fichier, TR1.element AS code, TR1.token_id, '{$this->name}', 0
FROM <rapport> TR1
LEFT JOIN <rapport> TR2
    ON TR2.module = 'affectations_variables' AND 
       TR1.element = TR2.element AND 
       TR1.fichier = TR2.fichier
WHERE TR1.module='variables' AND 
      TR2.element IS NULL
SQL;
    	$this->exec_query($query);

        $query = <<<SQL
DELETE FROM <rapport> CR1 
    WHERE CR1.element IN ('\$GLOBALS','\$_SESSION','\$_REQUEST',
                          '\$_GET','\$_POST','\$this','\$_FILES') AND
          CR1.module='{$this->name}'
SQL;
    	$this->exec_query($query);

        $query = <<<SQL
DELETE FROM CR1 
    USING <rapport> CR1, <rapport> CR2
WHERE CR1.module='{$this->name}' AND
      CR2.module='keyval' AND
      CR1.element = CR2.element AND
      CR1.fichier = CR2.fichier
SQL;
    	$this->exec_query($query);

	    return true;
	}
}

?>