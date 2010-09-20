<?php

class unused_properties extends modules {
	protected	$title = 'Propriétés non utilisées';
	protected	$description = 'Liste des propriétés définies, mais pas utilisées. ';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1 
WHERE scope='global'   AND 
      type ='variable' AND  
      class != '' AND  
      code NOT IN (  
         SELECT CONCAT('$', S2.code)
           FROM <tokens> S1
           JOIN <tokens> S2
             ON S2.fichier = S1.fichier AND 
                S2.droite BETWEEN S1.droite AND S1.gauche
           WHERE S1.class  = T1.class AND 
                 S1.scope != 'global'  AND 
                 S1.type   = 'property' AND 
                 S2.type='literals' 
                  )
SQL;
        $this->exec_query_insert('rapport', $query);
	}
}

?>