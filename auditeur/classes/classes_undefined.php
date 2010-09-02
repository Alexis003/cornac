<?php

class classes_undefined extends modules {
	protected	$title = 'Classes non définies';
	protected	$description = 'Liste des classes du code qui ne sont pas déclarées, mais qui sont utilisées. Les classes PHP sont omises.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('classes','_new');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $in = "'".join("','", modules::getPHPClasses())."'";
        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}', 0
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
        ON TR1.element = TR2.element AND TR2.module='classes' 
    WHERE TR1.module = '_new' AND 
          TR2.element IS NULL AND
          TR1.element NOT IN ($in)
SQL;
        $this->exec_query($query);
        return true;
	}
}

?>