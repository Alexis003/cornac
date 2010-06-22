<?php

class statiques extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();

	protected	$description = 'Liste des statiques (methodes, classes et proprités)';
	protected	$description_en = 'Literals being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T1.code,  T1.id, 'parentheses'
   FROM <tokens> T1
   WHERE T1.type != 'codephp AND
         T1.droite = 0';
SQL;
    $this->exec_query($requete);

// extraction du dernier? 
//select * from 
	}
	
}

?>