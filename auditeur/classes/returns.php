<?php

class returns extends modules {
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';
	protected	$description_en = 'List of action method from controlers in ZF ';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT 0, T1.fichier, concat(sum(if (type='_return',1,0)), ' returns'), T1.id, '{$this->name}' 
FROM caceis T1
WHERE scope NOT IN ( '__construct','__destruct','__set','__get','__call','__clone','__toString','__wakeup','__sleep') 
 AND scope != class AND (class != 'global' AND scope != 'global')
GROUP BY fichier, class, scope 
SQL;
    $this->exec_query($requete);
	}
}

?>