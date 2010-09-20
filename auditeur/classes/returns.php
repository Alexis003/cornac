<?php

class returns extends modules {
	protected	$title = 'Returns';
	protected	$description = 'Liste des utilisations de la commande return';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("sum(type='_return')", "' returns'");
        $query = <<<SQL
SELECT NULL, T1.fichier, $concat, T1.id, '{$this->name}' , 0
FROM <tokens> T1
WHERE scope NOT IN ( '__construct','__destruct','__set','__get','__call','__clone','__toString','__wakeup','__sleep') 
 AND scope != class AND (class != 'global' AND scope != 'global')
GROUP BY fichier, class, scope 
SQL;
        $this->exec_query_insert('rapport',$query);

        return true;
	}
}

?>