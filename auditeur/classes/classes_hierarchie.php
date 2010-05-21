<?php

class classes_hierarchie extends modules {
	protected	$description = 'Classe hierarchie';
	protected	$description_en = 'List of classes et its extensions';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport_dot> 
    SELECT distinct T2.code, T2.class,'', '{$this->name}'
    FROM <tokens_tags> TT
    JOIN <tokens> T2
       ON TT.token_sub_id = T2.id
    WHERE TT.type = 'extends';
SQL;
        $this->exec_query($requete);
    }
}

?>