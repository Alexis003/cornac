<?php

class affectations_variables extends modules {
	protected	$description = 'Noms des variables affectées dans l\'application';
	protected	$description_en = 'Name of the variables that are actually assigned within the application';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

// variables simples        
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T2.code, T1.id,'{$this->name}'  
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite = T1.droite + 1
WHERE T1.type = 'affectation'  AND T2.type = 'variable'
SQL;
        $this->exec_query($requete);    

// tableaux
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T3.code, T1.id,'{$this->name}'  
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite = T1.droite + 1
JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND T3.droite = T1.droite + 2
WHERE T1.type = 'affectation'  AND T2.type = 'tableau'
SQL;
        $this->exec_query($requete);    

// propriete
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T2.code, T1.id,'{$this->name}'  
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite = T1.droite + 1
WHERE T1.type = 'affectation'  AND T2.type = 'property'
SQL;
        $this->exec_query($requete);    

// propriete statique
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T3.code, T1.id,'{$this->name}'  
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite = T1.droite + 1
JOIN <tokens_tags> TT
    ON TT.token_id = T2.id AND TT.type = 'property'
JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND T3.id = TT.token_sub_id
WHERE T1.type = 'affectation'  AND T2.type = 'property_static'
SQL;
        $this->exec_query($requete);    

// cas de list()

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T4.code, T1.id,'{$this->name}'  
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite = T1.droite + 1 AND T2.type = 'functioncall'
JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND T3.droite = T1.droite + 2 AND T3.type = 'token_traite' AND T3.code = 'list'
JOIN <tokens> T4
    ON T1.fichier = T4.fichier AND T4.droite BETWEEN T2.droite AND T2.gauche AND T4.type = 'variable'
WHERE T1.type = 'affectation' 
SQL;
        $this->exec_query($requete);    
    }
}

?>