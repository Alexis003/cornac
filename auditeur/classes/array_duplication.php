<?php

class array_duplication extends modules {
	protected	$description = 'Foreach duplicant un tableau';
	protected	$description_en = 'Foreach making a copy of an array';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT 0, T1.fichier, T2.code, T1.id, '{$this->name}'
   FROM <tokens>  T1
   JOIN <tokens> T2
       ON T1.droite + 1 = T2.droite AND 
          T1.fichier = T2.fichier
   WHERE T1.type = '_new';
SQL;

        $this->exec_query($requete);

	}
}


/*
foreach.tableau != foreach.block.tableau

=> foreach.key = foreach.block.tableau.index
select T1.id, T1.type, T2.id, T2.code from tu T1
  join tu_tags TT
    ON TT.token_id = T1.id AND TT.type='key'
  join tu T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id=T2.id
where T1.type = '_foreach' AND T1.fichier = './tests/auditeur/scripts/array_duplication.php';

=> foreach.value
select T1.id, T1.type, T2.id, T2.code from tu T1
  join tu_tags TT
    ON TT.token_id = T1.id AND TT.type='value'
  join tu T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id=T2.id
where T1.type = '_foreach' AND T1.fichier = './tests/auditeur/scripts/array_duplication.php';


select * from tu T1 where T1.type='affectation' AND T1.fichier = './tests/auditeur/scripts/array_duplication.php' ;


=> foreach.valeur = foreach.block.affectation.gauche.index

select T1.id, T1.type, T2.id, T2.code from tu T1
  join tu_tags TT
    ON TT.token_id = T1.id AND TT.type='key'
  join tu T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id=T2.id
where T1.fichier = './tests/auditeur/scripts/array_duplication.php';

Suivre les variables? Tags next et prev

*/
?>