<?php

class classes_hierarchie extends modules {
	protected	$title = 'Hiérarchie des classes';
	protected	$description = 'Hierarchie de classe';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport_dot> 
    SELECT distinct T2.code, T2.class,'', '{$this->name}'
    FROM <tokens_tags> TT
    JOIN <tokens> T2
       ON TT.token_sub_id = T2.id
    WHERE TT.type = 'extends';
SQL;
    
        $this->exec_query($query);
    }
}

?>