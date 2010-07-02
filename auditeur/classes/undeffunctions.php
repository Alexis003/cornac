<?php

class undeffunctions extends modules {
	protected	$description = 'Liste des fonctions sans definitions';
	protected	$description_en = 'List of undefined functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}'
   FROM <rapport> TR1
   LEFT JOIN <rapport> TR2 
      ON TR1.element = TR2.element AND TR2.module='deffunctions'
   WHERE TR1.module='functionscalls' AND
         TR2.element IS NULL;
SQL;
//        print $this->prepare_query($requete);
        $this->exec_query($requete);

	}
}

?>