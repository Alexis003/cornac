<?php

class globals extends modules {
    protected    $description = 'Liste des variables globales utilisées';
    protected    $description_en = 'Global variable list';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
        $this->functions = array();
    }
    
    public function analyse() {
// variables marquées comme globales avec global

        $this->clean_rapport();
        
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, T2.fichier, T2.code AS code, T2.id, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2 
        ON T1.droite + 1 = T2.droite AND
           T1.fichier = T2.fichier
    WHERE T1.type='_global' 
SQL;
        $res = $this->exec_query($requete);
        
// variables globales via $GLOBALS
       $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, T1.fichier, T3.code AS code, T2.id, '{$this->name}'
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
        $res = $this->exec_query($requete);
    }
}

?>