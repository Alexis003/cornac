<?php

class constantes_classes extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';

	protected	$description = 'Liste des constantes de classe';
	protected	$description_en = 'Class constants being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

// cas simple : variable -> method
        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, TC.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens_cache> TC ON T1.id = TC.id
    WHERE T1.type = "constante_static"
SQL;
        $this->exec_query($requete);
	    return;
	}
//	    $this->in = array('constante_classe','constante_static');
	
}

?>