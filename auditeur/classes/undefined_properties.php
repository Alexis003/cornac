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

        $requete = <<<SQL
DROP TABLE IF EXISTS undefined_properties
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
CREATE TABLE undefined_properties
            SELECT DISTINCT right(code, length(code) - 1) as code, class FROM <tokens> 
            WHERE scope='global'  AND 
                  type ='variable'
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
ALTER TABLE undefined_properties ADD UNIQUE (code, class)
SQL;
        $this->exec_query($requete);



// @note only works on the same classe. Doesn't take into account hierarchy
        $requete = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
   FROM <tokens> T1
   JOIN <tokens> T2
     ON T2.fichier = T1.fichier AND 
        T2.droite BETWEEN T1.droite AND T1.gauche
   LEFT JOIN undefined_properties 
     ON undefined_properties.code = T2.code AND
        undefined_properties.class = T2.class 
   WHERE T1.scope!='global'  AND 
          T1.type ='property' AND 
          T2.type='literals'  AND 
          undefined_properties.code IS NULL
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
DROP TABLE undefined_properties
SQL;
        $this->exec_query($requete);

	}
	
}

?>