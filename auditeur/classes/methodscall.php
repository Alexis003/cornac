<?php

class methodscall extends modules {
    protected $title = "Méthodes appelées";
    protected $description = "Liste des appels de méthodes";

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}', 0
        FROM <tokens> T1
        JOIN <tokens_cache> T2 
            ON T1.id = T2.id
        WHERE
            T1.type='method'
SQL;

        $this->exec_query($query);

        return true;
	}
}

?>
