<?php

class functionscalls extends modules {
    protected $not = false; 

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $functions = get_defined_functions();
	    $extras = array('echo','print','die','exit','isset','empty','array','list','unset');
	    $total = array_merge($functions['internal'], $extras);
	    $in = join("', '", $total);

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
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
        print $this->prepare_query($requete);
//        die();
        $this->exec_query($requete);


	}
}

?>
