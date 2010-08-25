<?php

class block_of_call extends modules {
	protected	$description = 'Identifie une liste d\'appels successifs à la même fonction';
	protected	$description_en = 'Spot several call to the same function';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	public function analyse() {
        $this->clean_rapport();

// @todo : inclusions to be handler later
// @todo           LEFT(TC1.code, GREATEST(LOCATE('(', TC1.code), LOCATE(' ', TC1.code))) = LEFT(TC3.code, GREATEST(LOCATE('(', TC3.code), LOCATE(' ', TC3.code)))

        $query = <<<SQL
SELECT T1.id AS id1, T2.id AS id2, T3.id AS id3, T1.droite, T3.gauche, TC1.code AS code1, TC2.code AS code2, TC3.code AS code3, T1.fichier, LEFT(TC1.code, LOCATE('(', TC1.code) ) AS code
    FROM <tokens> T1
    JOIN <tokens> T2 
        ON T1.fichier=  T2.fichier AND T2.droite = T1.gauche + 1
    JOIN <tokens> T3
        ON T1.fichier=  T3.fichier AND T3.droite = T2.gauche + 1
    JOIN <tokens_cache> TC1 
        ON T1.id=  TC1.id
    JOIN <tokens_cache> TC2 
        ON T2.id=  TC2.id
    JOIN <tokens_cache> TC3
        ON T3.id=  TC3.id
    WHERE T1.type IN ('functioncall') AND 
          T2.type = T1.type AND 
          T3.type = T1.type AND
          LEFT(TC1.code, LOCATE('(', TC1.code)) = LEFT(TC3.code, LOCATE('(', TC3.code) )
    ORDER BY T1.id
SQL;
        $res = $this->exec_query($query);
        $resultats = array();
        $already = array();
        
        // @todo : reduce the number of partial list of functions
        while($ligne = $res->fetch()) {
           if (isset($already[$ligne['id1']])) {
                continue;
           }

           $resultats[$ligne['id1']] = array($ligne['id1'] => $ligne['code1'],
                                             $ligne['id2'] => $ligne['code2'],
                                             $ligne['id3'] => $ligne['code3']);
           $already[$ligne['id1']] = $ligne['fichier'];
           $already[$ligne['id2']] = $ligne['fichier'];
           $already[$ligne['id3']] = $ligne['fichier'];

           $id = $ligne['id3'];
           while ($id > 0) {
               $query2 = <<<SQL
SELECT T2.id, T1.droite, T1.type, T2.type, TC2.code AS code, T1.fichier
    FROM <tokens> T1
    LEFT JOIN <tokens> T2 
        ON T2.fichier = '{$ligne['fichier']}' AND T2.droite = T1.gauche + 1
    JOIN <tokens_cache> TC2 
        ON T2.id=  TC2.id
    WHERE T1.id = {$id} AND
          T1.fichier = '{$ligne['fichier']}' AND 
          '{$ligne['code']}' = LEFT(TC2.code, LOCATE('(', TC2.code) )
    LIMIT 12;
SQL;
                $res2 = $this->exec_query($query2);
                if ($ligne2 = $res2->fetch()) {
                   $already[$ligne2['id']] = $ligne['fichier'];
                    $resultats[$ligne['id1']][$ligne2['id']] = $ligne2['code'];
                    $id = $ligne2['id'];
                } else {
                    $id = 0;
                }
             }
         }
        
        foreach($resultats as $resultat) {
            list($id, $code) = each($resultat);
            $code = join("\n", $resultat);
            $query = <<<SQL
INSERT INTO <rapport> VALUES 
(NULL, '{$already[$id]}','$code','$id', '{$this->name}' )
SQL;

            $this->exec_query($query);
        }

	}
}

?>