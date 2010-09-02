<?php

class dot extends modules {

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $type_token = $this->noms['type_token'];
	    $type_tag = $this->noms['type_tags'];
        $this->noms = array();

        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
   SELECT T1.fichier, T2.code, T1.id, '{$this->name}', 0
   FROM <tokens> T1
    JOIN <tokens_tags> TT 
        ON T1.id = TT.token_id  
    JOIN <tokens> T2 
        ON TT.token_sub_id = T2.id
    WHERE T1.type='$type_token'      AND 
          TT.type = '$type_tag';
SQL;

        $this->exec_query($query);
    }
}

?>
