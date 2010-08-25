<?php

class undefined_properties extends modules {
	protected	$description = 'Liste des propriétés utilisées mais pas définies';
	protected	$description_en = 'List of used properties, that has no definition';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
DROP TABLE IF EXISTS undefined_properties
SQL;
        $this->exec_query($query);

        $query = <<<SQL
CREATE TEMPORARY TABLE {$this->name}_tmp
            SELECT DISTINCT right(code, length(code) - 1) as code, class FROM <tokens> 
            WHERE scope='global'  AND 
                  type ='variable'
SQL;
        $this->exec_query($query);

        $query = <<<SQL
ALTER TABLE {$this->name}_tmp ADD UNIQUE (code(500), class)
SQL;
        $this->exec_query($query);



// @note only works on the same classe. Doesn't take into account hierarchy
        $query = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
   FROM <tokens> T1
   JOIN <tokens> T2
     ON T2.fichier = T1.fichier AND 
        T2.droite BETWEEN T1.droite AND T1.gauche
   LEFT JOIN {$this->name}_tmp TMP 
     ON TMP.code = T2.code AND
        TMP.class = T2.class 
   WHERE T1.scope!='global'  AND 
          T1.type ='property' AND 
          T2.type='literals'  AND 
          TMP.code IS NULL
SQL;
        $this->exec_query($query);

	}
	
}

?>