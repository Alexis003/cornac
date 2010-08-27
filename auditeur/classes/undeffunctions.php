<?php

class undeffunctions extends modules {
	protected	$title = 'Fonctions non définies';
	protected	$description = 'Liste des fonctions sans définition ni déclaration : elles peuvent manquer, ou bien être natives à PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}'
   FROM <rapport> TR1
   LEFT JOIN <rapport> TR2 
      ON TR1.element = TR2.element AND TR2.module='deffunctions'
   WHERE TR1.module='functionscalls' AND
         TR2.element IS NULL;
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>