<?php

class php_classes extends functioncalls {
	protected	$description = 'Liste des classes PHP utilisées';
	protected	$title = 'Classes PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
	    return array('_new');
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $in = join("', '", modules::getPHPClasses());

        $query = <<<SQL
INSERT INTO <rapport> 
    SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}', 0
    FROM <tokens> T1 
    JOIN <tokens> T2
        ON T2.droite = T1.droite + 1 AND
           T2.fichier = T1.fichier
    WHERE T1.type='_new' AND 
          T2.code IN ('$in')
SQL;
        $this->exec_query($query);
        
        return true;
    }
}

?>