<?php

class multi_def_files extends modules {
	protected	$description = 'Fichier définissant plusieurs structures';
	protected	$description_en = 'Files defining several structures (classes or functions)';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
    CREATE TEMPORARY TABLE multi_def_files
    SELECT fichier FROM <tokens> 
        GROUP BY concat(class,'::', scope) 
        HAVING COUNT(*) > 1
SQL;
        $res = $this->exec_query($requete);
    
	    $requete = <<<SQL
INSERT INTO <rapport> 
    SELECT NULL, T1.fichier, concat(T1.class,'::', T1.scope), 0,  '{$this->name}' 
    FROM <tokens> T1
    JOIN multi_def_files ON
        T1.fichier = multi_def_files.fichier
    WHERE T1.class != '' AND T1.scope != 'global'
    GROUP BY T1.fichier
SQL;
        $res = $this->exec_query($requete);

	    $requete = <<<SQL
DROP TABLE multi_def_files
SQL;
        $res = $this->exec_query($requete);
    }
}

?>