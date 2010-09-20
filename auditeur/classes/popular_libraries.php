<?php 

class popular_libraries extends modules {
	protected	$title = 'Bibliothèques courantes';
	protected	$description = 'Identifie les structures de différentes bibliothèques courantes.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('classes');
	}
	
	public function analyse() {
        $this->clean_rapport();
// @todo use also constantes
// @todo use also functions
// @todo spot versions? 

        $list = parse_ini_file('../dict/poplib.ini', true);
        
        foreach($list as $ext => $characteristics) {
            $in = "'".join("', '", $characteristics['classes'])."'";

            // @doc search for usage as class extensions
            $query = <<<SQL
SELECT NULL, T1.fichier, '$ext', T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND
       TT.type = 'extends'
JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND
       T1.fichier = T2.fichier AND 
       T2.code IN ($in)
WHERE T1.type='_class'; 
SQL;
            $this->exec_query_insert('rapport', $query);

            // @doc search for usage as instanciation
            $query = <<<SQL
SELECT NULL, TR.fichier, '$ext', TR.id, '{$this->name}', 0
FROM <rapport> TR
WHERE TR.element IN ($in); 
SQL;
            $this->exec_query_insert('rapport', $query);
        }

        
        return true;
	}
}

?>