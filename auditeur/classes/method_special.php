<?php

class method_special extends modules {
	protected	$title = 'Méthodes spéciales';
	protected	$description = 'Liste des méthodes spéciales de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T1.class","'->'","T1.scope");
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, $concat, T1.id, '{$this->name}' , 0
        FROM <tokens> T1
        WHERE scope IN ( '__construct','__toString','__destruct',
                         '__set','__get','__call','__callStatic',
                         '__clone','__toString','__unset','__isset','__set_state',
                         '__invoke',
                         '__wakeup','__sleep'
                         ) 
               OR scope = class 
        GROUP BY fichier, class, scope;

SQL;
    $this->exec_query($query);

        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.scope, T1.id, '{$this->name}' , 0
        FROM <tokens> T1
        WHERE 
            scope IN ( '__autoload' ) AND 
            T1.type='_function';

SQL;
        $this->exec_query($query);
	}
}

?>