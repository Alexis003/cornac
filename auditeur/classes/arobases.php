<?php

class arobases extends modules {
    protected    $description = 'Arobases';
    protected    $title = 'Utilisateur de l\'opérateur @ dans le code';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
    }
    
    public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TC.fichier, TC.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1
    LEFT JOIN <tokens_cache>  TC 
    ON T1.id = TC.id 
    WHERE T1.type='noscream' 
SQL;
        $this->exec_query($query);
        
        return true;
    }
}

?>