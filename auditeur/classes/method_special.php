<?php

class method_special extends modules {
	protected	$description = 'Liste des méthodes spéciales de PHP';
	protected	$description_en = 'List of special methods of PHP';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
SELECT 0, T1.fichier, concat(T1.class,'->',T1.scope), T1.id, '{$this->name}' 
FROM <tokens> T1
WHERE scope IN ( '__construct','__destruct','__set','__get','__call','__clone','__toString','__wakeup','__sleep') 
 OR scope = class 
GROUP BY scope;

SQL;

    print $this->prepare_query($requete);
    $this->exec_query($requete);

	}
}

?>