<?php

class classes_hierarchie extends modules {
	protected	$description = 'Classe hierarchie';
	protected	$description_en = 'List of classes et its extensions';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM rapport_dot WHERE module='{$this->name}'
SQL;
        $this->mid->query($requete);

        $requete = <<<SQL
INSERT INTO rapport_dot 
    SELECT T2.code, T2.class,'', '{$this->name}' FROM tokens_tags
    JOIN tokens T2
       ON tokens_tags.token_sub_id = T2.id
    WHERE tokens_tags.type = 'extends';
SQL;
        $this->mid->query($requete);

//        $this->updateCache();
    }
}

?>