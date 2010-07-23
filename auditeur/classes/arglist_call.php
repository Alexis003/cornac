<?php

class arglist_call extends modules {
	protected	$description = 'Liste des arguments par appel de fonction';
	protected	$description_en = 'List of argument for called functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, CONCAT(T2.code,'(', count(*),' args)') AS code, T1.id, '{$this->name}'
FROM <tokens> T1
JOIN <tokens_tags> TT1
    ON T1.id = TT1.token_id AND TT1.type = 'fonction'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND TT1.token_sub_id = T2.id
JOIN <tokens_tags> TT2
    ON T1.id = TT2.token_id AND TT2.type='args'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND TT2.token_sub_id = T3.id AND T3.type = 'arglist'
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND T4.droite BETWEEN T3.droite AND T3.gauche AND T4.level = T3.level + 1
WHERE T1.type = 'functioncall'
GROUP BY T1.id;
SQL;
        $this->exec_query($requete);

        return ;
	}
}

?>