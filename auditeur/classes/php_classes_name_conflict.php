<?php

class php_classes_name_conflict extends modules {
	protected	$title = 'Conflits de noms avec des classe PHP';
	protected	$description = 'Identifie des classes dont le nom est en conflit avec celles courantes de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('classes');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $classes = modules::getPHPClasses();
        $classes = join("','", $classes);

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.element, T1.id, '{$this->name}', 0
    FROM <rapport> T1
    WHERE   T1.module = 'classes' AND
            T1.element IN ('$classes')
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>