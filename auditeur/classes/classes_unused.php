<?php

class classes_unused extends modules {
	protected	$description = 'Liste des classes qui ne sont pas utilisées';
	protected	$description_en = 'List of unused classes';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}

	function dependsOn() {
	    return array('classes','_new','classes_hierarchie');
	}
	
	public function analyse() {
        $this->clean_rapport();

// this will take care of direct instantiation classes 
// IN will take care of extensions
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}'
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
    ON TR1.element = TR2.element AND TR2.module='_new' 
    WHERE TR1.module = 'classes' AND TR2.module IS NULL
SQL;
        $this->exec_query($requete);

// we need to check extensions : we have them in the dot rapport, from classes_hierarchie
        $requete = <<<SQL
SELECT TRD.a
    FROM <rapport>  TR1
    JOIN <rapport_dot> TRD
      ON TRD.b = TR1.element
    WHERE TR1.module = '_new' AND TRD.module = 'classes_hierarchie'
SQL;
        $res = $this->exec_query($requete);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $requete = <<<SQL
DELETE FROM <rapport> 
    WHERE module='{$this->name}' AND element IN ('$in')
SQL;
        $res = $this->exec_query($requete);

// same as above, but with 2 levels for extensions
        $requete = <<<SQL
SELECT TRD2.a
    FROM <rapport>  TR1
    JOIN <rapport_dot> TRD1
      ON TRD1.b = TR1.element
    JOIN <rapport_dot> TRD2
      ON TRD2.b = TRD1.a
    WHERE TR1.module = '_new' AND 
          TRD1.module = 'classes_hierarchie' AND
          TRD2.module = 'classes_hierarchie'
SQL;
        print $this->prepare_query($requete);
        $res = $this->exec_query($requete);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $requete = <<<SQL
DELETE FROM <rapport> 
    WHERE module='{$this->name}' AND element IN ('$in')
SQL;
        $res = $this->exec_query($requete);

// same as above, but with 3 levels for extensions
        $requete = <<<SQL
SELECT TRD3.a
    FROM <rapport>  TR1
    JOIN <rapport_dot> TRD1
      ON TRD1.b = TR1.element
    JOIN <rapport_dot> TRD2
      ON TRD2.b = TRD1.a
    JOIN <rapport_dot> TRD3
      ON TRD3.b = TRD2.a
    WHERE TR1.module = '_new' AND 
          TRD1.module = 'classes_hierarchie' AND
          TRD2.module = 'classes_hierarchie' AND 
          TRD3.module = 'classes_hierarchie'          
SQL;
        print $this->prepare_query($requete);
        $res = $this->exec_query($requete);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $requete = <<<SQL
DELETE FROM <rapport> 
    WHERE module='{$this->name}' AND element IN ('$in')
SQL;
        $res = $this->exec_query($requete);

// same as above, but with 4 levels for extensions
        $requete = <<<SQL
SELECT TRD4.a
    FROM <rapport>  TR1
    JOIN <rapport_dot> TRD1
      ON TRD1.b = TR1.element
    JOIN <rapport_dot> TRD2
      ON TRD2.b = TRD1.a
    JOIN <rapport_dot> TRD3
      ON TRD3.b = TRD2.a
    JOIN <rapport_dot> TRD4
      ON TRD4.b = TRD3.a
    WHERE TR1.module = '_new' AND 
          TRD1.module = 'classes_hierarchie' AND
          TRD2.module = 'classes_hierarchie' AND 
          TRD3.module = 'classes_hierarchie' AND 
          TRD4.module = 'classes_hierarchie'          
SQL;
        print $this->prepare_query($requete);
        $res = $this->exec_query($requete);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $requete = <<<SQL
DELETE FROM <rapport> 
    WHERE module='{$this->name}' AND element IN ('$in')
SQL;
        $res = $this->exec_query($requete);

// may we need some more, or a while loop ...

        return ;
	}
}

?>