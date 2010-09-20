<?php

class regex extends modules {
	protected	$title = 'Regex';
	protected	$description = 'Liste des expressions rationnelles identifiées dans le code.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
   SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}', 0
   FROM <tokens> T1
   JOIN <tokens> T2
   ON T2.fichier = T1.fichier AND
      T2.droite = T1.droite + 3
   WHERE T1.code in ('preg_match','preg_replace','preg_replace_callback','preg_match_all')
SQL;
        $this->exec_query_insert('rapport',$query);

        return true;
	}
}

?>