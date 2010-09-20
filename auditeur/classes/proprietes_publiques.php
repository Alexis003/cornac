<?php

class proprietes_publiques extends modules {
	protected	$description = 'Liste des propriétés publiques';
	protected	$title = 'Propriétés publiques';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        // @doc cas of simple public var
        $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T1.class,'::',T2.code), T1.id,  'proprietes_publiques', 0
    FROM <tokens> T1 
    LEFT JOIN <tokens> T2
        ON T1.droite + 1 = T2.droite AND
           T1.fichier = T2.fichier 
   WHERE T1.type = '_var' AND
         T2.type = 'variable';
SQL;
        $this->exec_query_insert('rapport',$query);

        // @doc cas of simple public var
        $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T1.class,'::',T3.code), T1.id,  'proprietes_publiques', 0
    FROM <tokens> T1 
    LEFT JOIN <tokens> T2
        ON T1.droite + 1 = T2.droite AND
           T1.fichier = T2.fichier 
    JOIN <tokens> T3
        ON T1.fichier = T2.fichier AND 
           T3.droite = T1.droite + 3 AND
           T1.fichier = T3.fichier AND
           T3.type != 'token_traite'
   WHERE T1.type = '_var' AND
         T2.code = 'public';
SQL;
        $this->exec_query_insert('rapport',$query);

        $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T1.class,'::',T4.code), T1.id,  'proprietes_publiques', 0
    FROM <tokens> T1 
    JOIN <tokens> T2
        ON T1.droite + 1 = T2.droite AND
           T1.fichier = T2.fichier AND 
           T2.type = 'token_traite'
    JOIN <tokens> T3
        ON T1.fichier = T2.fichier AND 
           T3.droite = T1.droite + 3 AND
           T1.fichier = T3.fichier AND
           T3.type = 'token_traite'
    JOIN <tokens> T4
        ON T1.fichier = T4.fichier AND 
           T4.droite = T1.droite + 5 AND
           T1.fichier = T4.fichier AND
           T4.type != 'token_traite'
   WHERE T1.type = '_var' AND
         (T2.code = 'public' OR T3.code='public');
SQL;
        $this->exec_query_insert('rapport',$query);

    // @todo support class and methods
    // @todo support also static and var keyword
    
        return true;
    }
}

?>