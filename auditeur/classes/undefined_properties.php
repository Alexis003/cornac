<?php

class undefined_properties extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();

	protected	$description = 'Liste des propriétés utilisées mais pas définies';
	protected	$description_en = 'List of used properties, that has no definition';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();


// @note only works on the same classe. Doesn't take into account hierarchy
        $requete = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
   FROM <tokens> T1
   JOIN <tokens> T2
     ON T2.fichier = T1.fichier AND 
        T2.droite BETWEEN T1.droite AND T1.gauche
    WHERE T1.scope!='global'  AND 
          T1.type ='property' AND 
          T2.type='literals'  AND 
          CONCAT('$',T2.code) NOT IN (
            SELECT code FROM <tokens> 
            WHERE class=T1.class  AND 
                  scope='global'  AND 
                  type ='variable'
          )
SQL;
        $this->exec_query($requete);
	}
	
}

?>