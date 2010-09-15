<?php 

class multidimarray extends modules {
	protected	$title = 'Tableaux multi-dimensionnels';
	protected	$description = 'Liste les tableaux utilisées de manière multi-dimensionnelles, que ce soit par appel ($x[1][2]) ou par construction (array(array())';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, TC.code ,T1.id, '{$this->name}', 0
FROM <tokens> T1
/* JOIN */
JOIN <tokens_cache> TC
    ON TC.id = T1.id
LEFT JOIN <tokens> TX
    ON TX.type IN ('tableau','opappend') AND 
       T1.fichier = TX.fichier AND
       T1.droite - 1 = TX.droite
LEFT JOIN <rapport> TR
    ON TR.module='{$this->name}' AND
       TR.token_id = T1.id
WHERE T1.type IN ('tableau','opappend') AND
      TR.id IS NULL AND
      TX.id IS NULL
SQL;

for($i = 2; $i < 7; $i++) {
    $h = $i - 1;
    $join = <<<SQL
JOIN <tokens> T$i
    ON T$i.type IN ('tableau','opappend') AND 
       T1.fichier = T$i.fichier AND
       T$h.droite + 1 = T$i.droite
/* JOIN */
SQL;
    $query = str_replace('/* JOIN */', $join, $query);
    $query = str_replace('       T'.$h.'.droite + 1 = TX.droite','       T'.$i.'.droite + 1 = TX.droite', $query);

    $this->exec_query($query);
}

        // @todo spot array(array());
        
        return true;
	}
}

?>