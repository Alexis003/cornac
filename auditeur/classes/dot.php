<?php

class dot extends modules {

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $type_token = $this->noms['type_token'];
	    $type_tag = $this->noms['type_tags'];
        $this->noms = array();

        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM rapport_dot WHERE module='{$this->name}'
SQL;
        $this->mid->query($requete);

        $requete = <<<SQL
INSERT INTO rapport 
   SELECT T1.fichier, T2.code, T1.id, '{$this->name}'
   FROM tokens T1
    JOIN tokens_tags 
        ON T1.id = tokens_tags.token_id  
    JOIN tokens T2 
        ON tokens_tags.token_sub_id = T2.id
    WHERE T1.type='$type_token'      AND 
          tokens_tags.type = '$type_tag';
SQL;

        $this->mid->query($requete);

        $this->updateCache();
    }
}

?>
