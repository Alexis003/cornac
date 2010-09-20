<?php

class parentheses extends modules {
    protected    $title = 'Parentheses';
    protected    $description = 'Utilisation des parenthèses';

    function __construct($mid) {
        parent::__construct($mid);
    }
    
    public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, T1.fichier, T2.code,  T1.id, 'parentheses', 0
FROM <tokens> T1
JOIN <tokens_cache> T2
    ON T1.id = T2.id
WHERE T1.type = 'parentheses';
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
    }
}
?>