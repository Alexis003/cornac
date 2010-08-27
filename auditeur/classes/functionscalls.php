<?php

class functionscalls extends modules {
    protected $description = "Liste des appels de fonctions"; 
    protected $title = "Appels de fonctions"; 

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $total = modules::getPHPFunctions();
	    $in = join("', '", $total);

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
  FROM <tokens> T1
  JOIN <tokens> T2
       ON T1.fichier = T2.fichier AND
          T1.droite = T2.droite - 1
  LEFT JOIN <tokens_tags> TT
       ON T1.id = TT.token_sub_id
where 
 T1.type='functioncall' AND
( TT.token_id IS NULL OR TT.type != 'methode') AND
T2.code NOT IN ('$in')
SQL;

        $this->exec_query($query);
        return true;
	}
}

?>
