<?php

class arglist_def extends modules {
	protected	$description = 'Liste des arguments par definitions de fonctions';
	protected	$description_en = 'List of argument for defined functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	public function analyse() {
        $this->clean_rapport();

// @doc this query search for the minimum argument to send a function/method
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, CONCAT(T2.code,'(', count(*),' args)') AS code, T1.id, '{$this->name}'
FROM <tokens> T1
JOIN <tokens_tags> TT1
    ON T1.id = TT1.token_id AND TT1.type = 'name'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND TT1.token_sub_id = T2.id
JOIN <tokens_tags> TT2
    ON T1.id = TT2.token_id AND TT2.type='args'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND TT2.token_sub_id = T3.id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND T4.droite BETWEEN T3.droite AND T3.gauche
    AND T4.type = 'variable'
    AND T4.level = T3.level + 1
WHERE T1.type = '_function'
GROUP BY T1.id;
SQL;
        $this->exec_query($requete);

// @doc this query search for variable number of argument
        $requete = <<<SQL
SELECT NULL, T1.fichier, 
       SUM(IF(T4.type='variable',1,0)) AS compulsory, 
       SUM(IF(T4.type='arginit',1,0)) AS optional, 
       T2.code AS code,
       T1.id
FROM <tokens> T1
JOIN <tokens_tags> TT1
    ON T1.id = TT1.token_id AND TT1.type = 'name'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND TT1.token_sub_id = T2.id
JOIN <tokens_tags> TT2
    ON T1.id = TT2.token_id AND TT2.type='args'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND TT2.token_sub_id = T3.id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND T4.droite BETWEEN T3.droite AND T3.gauche
    AND T4.level = T3.level + 1
WHERE T1.type = '_function'
GROUP BY T1.id
HAVING optional > 0
;
SQL;
        $res = $this->exec_query($requete);
        
        while($row = $res->fetch()) {
            for($i = 0; $i < $row['optional']; $i++) {
                $nb = $row['compulsory'] + $i + 1;
                $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, '{$row['fichier']}', CONCAT('{$row['code']}','(', $nb ,' args)'), '{$row['id']}', '{$this->name}'
SQL;
                $this->exec_query($requete);
            }
        }
        return ;
    }
}

?>