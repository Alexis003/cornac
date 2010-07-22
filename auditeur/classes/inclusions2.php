<?php

class inclusions2 extends modules {
	protected	$description = 'Liste des inclusions vers dot';
	protected	$description_en = 'Where files are included to dot';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T1.fichier, T3.code,T1.fichier, '{$this->name}'
FROM <tokens> T1
    JOIN <tokens> T2
        ON T2.droite  = T1.droite + 1 AND
           T2.fichier = T1.fichier
    JOIN <tokens_cache> T3
        ON T3.id = T2.id
          AND T3.fichier = T2.fichier
	    WHERE T1.type='inclusion'
SQL;
        $res = $this->exec_query($requete);
        
       $requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T1.fichier, T2.code, T1.fichier, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T2.droite  = T1.droite + 1 AND
           T2.fichier = T1.fichier
	    WHERE T1.type='inclusion' AND
	          T2.type in ('literals','variable');
SQL;
        $res = $this->exec_query($requete);

        $concat = $this->concat('"inc/"','T4.code',"'/'",'T4.code',"'.inc'");
        
       $requete = <<<SQL
INSERT INTO <rapport_dot> 
  SELECT T1.fichier, REPLACE($concat,'"', ''), T1.fichier, '{$this->name}' 
  FROM <tokens> T1
  JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id
  JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND T1.fichier = T2.fichier and TT.type='fonction' and T2.code='loadLibrary'
  JOIN <tokens_tags> TT2 
    ON TT2.token_id = T1.id
  JOIN <tokens> T3
    ON TT2.token_sub_id = T3.id AND T1.fichier = T3.fichier and TT2.type='args'
  JOIN <tokens> T4
    ON T1.fichier = T4.fichier and T4.type='literals' AND T4.droite between T3.droite and T3.gauche
  WHERE T1.type='functioncall';
SQL;
        $res = $this->exec_query($requete);

       include_once('../libs/path_normaliser.php');
       $requete = <<<SQL
SELECT * FROM <rapport_dot> WHERE module='{$this->name}'
SQL;
        $res = $this->exec_query($requete);

    while($ligne = $res->fetch()) {
        $ligne['b'] = str_replace( array("\"", "'"), array('',''), $ligne['b']);
        
        $variables = array(
            '$this->absolutePath.' => './References/24hmans/inc/',
            '$g_path_inc.' =>  './References/24hmans/',
            '$g_path_temp.' => './References/24hmans/temp/',
            '$g_path_admin.' => './References/24hmans/administration-app/',
            '$g_physical_path.' => './References/24hmans/Cron/',
            '$name.' => 'ModuleManager',
            '$cache_name.' =>  'cache', 
            '$u_module.' => 'Cache', 
            '$u_path.' => 'Cache',
            
        );
        
        $ligne['b'] = str_replace(array_keys($variables), array_values($variables), $ligne['b']);
        
        $ligne['b'] = path_normaliser(dirname($ligne['a']).'/', $ligne['b']);

        if ($ligne['a'] == './References/24hmans/inc/ManyWebServices/ManyWebServices.php') {
            print_r($ligne);
        }

        $ligne['b'] = addslashes($ligne['b']);
        $ligne[1] = addslashes($ligne[1]);

       $requete = <<<SQL
UPDATE <rapport_dot>
   SET b = '{$ligne['b']}'
WHERE module='{$this->name}' AND 
      b = '{$ligne[1]}' AND
      a = '{$ligne['a']}'
SQL;
        $this->exec_query($requete);
//        print '.';
        
    }

/*
       $requete = <<<SQL
UPDATE <rapport_dot> 
    SET a = REPLACE(a, './References/24hmans/', ''),
        b = REPLACE(b, './References/24hmans/', '')
    WHERE module='{$this->name}'
SQL;

        $res = $this->exec_query($requete);
*/
/*

select * from many where code='loadLibrary';

select T1.fichier, REPLACE(CONCAT("inc/",T4.code,'/',T4.code,'.inc'),'"', ''), T1.fichier, '{$this->name}' from many T1
join many_tags TT 
ON TT.token_id = T1.id
join many T2
ON TT.token_sub_id = T2.id AND T1.fichier = T2.fichier and TT.type='fonction' and T2.code='loadLibrary'
join many_tags TT2 
ON TT2.token_id = T1.id
join many T3
ON TT2.token_sub_id = T3.id AND T1.fichier = T3.fichier and TT2.type='args'
join many T4
ON T1.fichier = T4.fichier and T4.type='literals' AND T4.droite between T3.droite and T3.gauche
WHERE T1.type='functioncall';


*/

	}
}

?>