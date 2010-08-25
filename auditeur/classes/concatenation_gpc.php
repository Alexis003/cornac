<?php

class concatenation_gpc extends modules {
	protected	$description = 'Concatenation utilisant un GPC';
	protected	$description_en = 'Concatenation using a GPC variable';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $concat = $this->concat('class','"::"','scope');
        $gpc_regexp = '(\\\\'.join('|\\\\',modules::getPHPGPC()).')';
	    $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2
    ON T1.fichier= T2.fichier AND 
        T2.type='variable' AND 
        T2.droite BETWEEN T1.droite AND T1.gauche AND
        T2.code REGEXP '^$gpc_regexp'
    WHERE T1.type='concatenation'

SQL;
        $this->exec_query($query);

	    return;
	}
}

?>
