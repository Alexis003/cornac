<?php

class arobases extends modules {
    protected    $description = 'Utilisation des arobases';
    protected    $description_en = 'Usage of @';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
    }
    
    public function analyse() {
        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM rapport WHERE type='$module'
SQL;
        $res = $this->mid->query($requete);

        $requete = <<<SQL
INSERT INTO rapport 
        SELECT 0, fichier, replace(replace(code,'\$"',''),"'",'') AS code, id, '$module'
    FROM tokens
    WHERE type='noscream'
SQL;

        $this->mid->query($requete);

        $this->updateCache();
        $this->functions = array();
    }
}

?>