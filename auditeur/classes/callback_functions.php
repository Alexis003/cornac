<?php 

class callback_functions extends modules {
	protected	$title = 'Fonction de rappel';
	protected	$description = 'Nom des fonctions de rappels utilisées. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

// @doc spot callback when it's a first argument
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T4.code, T4.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='fonction'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       TT.token_sub_id = T2.id AND
       T2.code IN (
'array_map',
'call_user_func',
'call_user_func_array'
)
JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND
       TT2.type='args'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       TT2.token_sub_id = T3.id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND
       T4.droite = T3.droite + 1
WHERE T1.type='functioncall';
SQL;
        $this->exec_query($query);

// @doc spot callback when it's a second argument
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T5.code, T5.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='fonction'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       TT.token_sub_id = T2.id AND
       T2.code IN (
                    'usort', 
                    'preg_replace_callback',
                    'uasort',
                    'uksort',
                    'array_reduce',
                    'array_walk',
                    'array_walk_recursive',
                    'mysqli_set_local_infile_handler'
                   )
JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND
       TT2.type='args'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       TT2.token_sub_id = T3.id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND
       T4.droite = T3.droite + 1
JOIN <tokens> T5
    ON T5.fichier = T1.fichier AND
       T5.droite = T4.gauche + 1
WHERE T1.type='functioncall';
SQL;
        $this->exec_query($query);

// @doc spot callback when it's the last argument
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T4.code, T4.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='fonction'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       TT.token_sub_id = T2.id AND
       T2.code IN (
                   'array_diff_uassoc',
                   'array_diff_ukey',
                   'array_intersect_uassoc',
                   'array_intersect_ukey',
                   'array_udiff_assoc',
                   'array_udiff_uassoc',
                   'array_udiff',
                   'array_uintersect_assoc',
                   'array_uintersect_uassoc',
                   'array_uintersect',
                   'array_filter',
                   'array_reduce'
)
JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND
       TT2.type='args'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       TT2.token_sub_id = T3.id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND
       T4.gauche = T3.gauche - 1
WHERE T1.type='functioncall';
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>