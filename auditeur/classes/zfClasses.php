<?php 

class zfClasses extends modules {
	protected	$title = 'Classes du Zend Framework';
	protected	$description = 'Repère les classes issues du Zend Framework';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $list = parse_ini_file('../dict/zfClasses.ini');
        $in = "'".join("', '", $list['classes'])."'";
        
// @todo of course, update this useless query. :)
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}', 0
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
        $this->exec_query($query);
        
        return true;
	}
}

?>