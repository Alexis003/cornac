<?php

class ifsanselse extends modules {
	protected	$description = 'Liste des if sans else';
	protected	$description_en = 'if without else';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T2.class","'->'","T2.code");
	    $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, SUM(TT.type = 'else')  AS elsee, T1.id, '{$this->name}'
    FROM <tokens> T1
    LEFT join <tokens_tags> TT ON
        T1.id = TT.token_id
    WHERE T1.type = 'ifthen' 
    GROUP by fichier, droite
    HAVING elsee = 0;
SQL;
        $this->exec_query($query);
	}
}

?>