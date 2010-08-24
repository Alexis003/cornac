<?php

class dangerous_combinaisons extends modules {
	protected	$description = 'Liste de fichiers ayant des combinaisons dangereuses d\'elements';
	protected	$description_en = 'List of files that combine several dangerous tokens';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('affectations_variables');
	}

	public function analyse() {
        $this->clean_rapport();
        
        $combinaisons = parse_ini_file('../dict/combinaisons.ini', true);

        foreach ($combinaisons as $nom => $combinaison) {
            $in = "'".join("','", $combinaison['combinaison'])."'";
            $count = count($combinaison['combinaison']);
            // @todo : this shouldn't be sufficient. One must work on distinct occurences... may be a sub query will do

            $query = <<<SQL
INSERT INTO <rapport> 
    SELECT NULL, T1.fichier, '$nom', T1.code, '{$this->name}'
    FROM <tokens> T1
    GROUP BY fichier
    HAVING SUM(IF (code IN ($in), 1, 0)) >= $count
SQL;
            $this->exec_query($query);
        }
        return ;
	}
}

?>