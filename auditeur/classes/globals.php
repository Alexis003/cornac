<?php

class globals extends modules {
    protected    $description = 'Liste des variables globales utilisées';
    protected    $description_en = 'Global variable list';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
    }
    
    public function analyse() {
        $this->clean_rapport();
        
        // variable global thanks to the global reserved word
        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T2.fichier, T2.code AS code, T2.id, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2 
        ON T1.droite + 1 = T2.droite AND
           T1.fichier = T2.fichier
    WHERE T1.type='_global' 
SQL;
        $this->exec_query($query);
        
// variables globales because in $GLOBALS
       $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T3.code AS code, T2.id, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2 
        ON T1.droite + 1 = T2.droite AND
           T1.fichier = T2.fichier
    LEFT JOIN <tokens_cache> T3
        ON T1.id = T3.id AND
           T1.fichier = T3.fichier
    WHERE 
          T1.type = 'tableau' AND
          T2.code = '\$GLOBALS';
SQL;
        $this->exec_query($query);

// variables in main are automatically globals
/*
       $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1
    WHERE 
        T1.scope = 'global' AND
        T1.type = 'variable'
SQL;
        $this->exec_query($query);
        */
    }    
    
}

?>