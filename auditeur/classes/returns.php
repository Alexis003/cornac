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

        $concat = $this->concat("sum(type='_return')", "' returns'");
        $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, $concat, T1.id, '{$this->name}' 
FROM <tokens> T1
WHERE scope NOT IN ( '__construct','__destruct','__set','__get','__call','__clone','__toString','__wakeup','__sleep') 
 AND scope != class AND (class != 'global' AND scope != 'global')
GROUP BY fichier, class, scope 
SQL;
    $this->exec_query($query);
	}
}

?>