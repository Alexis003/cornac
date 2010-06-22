<?php

class methodscall extends modules {
    protected $not = false; 

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T2.code AS code, T1.id, 'methodscall'
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
where 
 T1.type='method'
SQL;

        $this->exec_query($requete);


	}
}

?>
