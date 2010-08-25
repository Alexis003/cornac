<?php

class gpc_affectations extends modules {
	protected	$description = 'Liste des variables GPC qui sont affectées';
	protected	$description_en = 'List of GPC variables that are being assigned';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('affectations_variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $gpc_regexp = '(\\\\'.join('|\\\\',modules::getPHPGPC()).')';

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}'
FROM <rapport> TR1
WHERE TR1.module = 'affectations_variables' AND 
      TR1.element REGEXP '^$gpc_regexp'
SQL;
        $this->exec_query($query);

        return ;
	}
}

?>