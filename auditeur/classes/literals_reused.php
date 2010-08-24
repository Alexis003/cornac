<?php

class literals_reused extends modules { 
	protected	$title = 'Literaux utilisés plusieurs fois';
	protected	$description = 'Literaux qui sont réutiisés à plusieurs endroits du code';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('literals');
	}

	public function analyse() {
        $this->clean_rapport();

// @note temporary table, so has to avoid concurrency conflict
        $requete = <<<SQL
CREATE TEMPORARY TABLE {$this->name}_TMP 
SELECT TRIM(code) AS code
    FROM <tokens> TR1
    WHERE type = 'literals' AND 
          TRIM(code) != ''
    GROUP BY BINARY TRIM(code) 
    HAVING COUNT(*) > 1
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TRIM(TR1.code), TR1.id, '{$this->name}'
    FROM <tokens> TR1
    JOIN {$this->name}_TMP TMP
        ON TR1.type = 'literals' AND 
           TMP.code = TRIM(TR1.code) 
SQL;
        $this->exec_query($requete);

        return ;
	}
}

?>