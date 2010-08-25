<?php

class inclusions extends modules {
	protected	$description = 'Liste des inclusions';
	protected	$description_en = 'Where files are included';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}'
   FROM <tokens>  T1
   WHERE T1.type = 'inclusion';
SQL;
        $this->exec_query($query);

        $query = <<<SQL
INSERT INTO <rapport>
  SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}'
  FROM <tokens> T1
  JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id
  JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND
       T1.fichier = T2.fichier AND
       TT.type='fonction' 
       AND T2.code='loadLibrary'
SQL;
        $this->exec_query($query);

	}
}

?>